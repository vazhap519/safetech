import type { CalculatorField, CalculatorValues } from "./service-calculator";

type Device = {
    brand: string;
    model: string;
    ecosystem?: string;
    max_apartments?: number;
    max_doors?: number;
    max_per_apartment?: number;
    poe_ports?: number;
    poe_budget_w?: number;
    per_port_w?: number;
    dedicated_uplinks?: number;
};

export type IntercomCatalog = {
    doorStations: Record<string, Device>;
    indoorStations: Record<string, Device>;
    switches: Record<string, Device>;
    poeAllocationW: number;
    poeReserve: number;
    maxDistributionSwitches: number;
};

function bounded(value: unknown, min: number, max: number) {
    const number = Number(value);
    return Math.max(min, Math.min(max, Number.isFinite(number) ? Math.trunc(number) : min));
}

function enabled(value: unknown) {
    return value === true || value === 1 || ["1", "true", "on", "yes"].includes(String(value).toLowerCase());
}

export function intercomSwitchCapacity(catalog: IntercomCatalog, device: Device) {
    if ((device.per_port_w ?? 0) < catalog.poeAllocationW || (device.dedicated_uplinks ?? 0) < 1) return 0;
    return Math.min(device.poe_ports ?? 0, Math.floor((device.poe_budget_w ?? 0) / (catalog.poeAllocationW * catalog.poeReserve)));
}

export function intercomDeviceOptions(catalog: IntercomCatalog, key: string, values: CalculatorValues) {
    if (key === "door_station_id") {
        return Object.entries(catalog.doorStations).filter(([, d]) => (d.max_apartments ?? 0) >= Number(values.apartments) && (d.max_doors ?? 0) >= Number(values.doors));
    }
    if (key === "indoor_station_id") {
        const door = catalog.doorStations[String(values.door_station_id)];
        return Object.entries(catalog.indoorStations).filter(([, d]) => d.ecosystem === door?.ecosystem && (d.max_per_apartment ?? 0) >= Number(values.monitors_per_apartment));
    }
    const endpoints = Number(values.apartments) * Number(values.monitors_per_apartment) + Number(values.doors);
    return Object.entries(catalog.switches).filter(([, d]) => {
        const capacity = intercomSwitchCapacity(catalog, d);
        return capacity > 0 && Math.ceil(endpoints / capacity) <= catalog.maxDistributionSwitches;
    });
}

/** Mirrors IntercomPlanner using the server's catalog; no model specs are duplicated. */
export function intercomValues(catalog: IntercomCatalog, input: CalculatorValues): CalculatorValues {
    const c: CalculatorValues = { ...input };
    for (const [key, min, max] of [
        ["apartments", 1, 100], ["doors", 1, 10], ["monitors_per_apartment", 1, 6],
        ["cards_per_apartment", 0, 10], ["cable_meters", 0, 100000],
        ["lock_cable_meters", 0, 100000], ["conduit_meters", 0, 100000],
    ] as const) c[key] = bounded(c[key], min, max);
    for (const key of ["door_closer", "door_contact", "backup_power", "external_reader", "sd_cards", "surge_protection", "remote_access"]) c[key] = enabled(c[key]);
    c.lock_current_a = Math.max(0.1, Math.min(5, Number(c.lock_current_a) || 0.1));
    c.lock_type = ["maglock", "strike", "bolt"].includes(String(c.lock_type)) ? c.lock_type : "maglock";
    c.exit_type = c.exit_type === "touchless" ? "touchless" : "button";
    for (const key of ["door_station_id", "indoor_station_id"]) {
        const options = intercomDeviceOptions(catalog, key, c);
        if (!options.some(([id]) => id === c[key])) c[key] = options[0]?.[0] ?? "";
    }
    const switches = intercomDeviceOptions(catalog, "switch_id", c);
    if (!switches.some(([id]) => id === c.switch_id)) c.switch_id = "auto";
    const monitors = Number(c.apartments) * Number(c.monitors_per_apartment);
    const endpoints = monitors + Number(c.doors);
    const chosen = c.switch_id === "auto"
        ? switches.find(([, d]) => intercomSwitchCapacity(catalog, d) >= endpoints) ?? switches[switches.length - 1]
        : switches.find(([id]) => id === c.switch_id);
    if (!chosen) return c;
    const [id, device] = chosen;
    const count = Math.ceil(endpoints / intercomSwitchCapacity(catalog, device));
    const core = count > 1 ? 1 : 0;
    return {
        ...c, monitor_count: monitors, endpoint_count: endpoints,
        resolved_switch_id: id, switch_count: count,
        switch_usable_ports: intercomSwitchCapacity(catalog, device),
        poe_required_w: Math.round(endpoints * catalog.poeAllocationW * catalog.poeReserve * 100) / 100,
        poe_available_w: count * (device.poe_budget_w ?? 0),
        poe_available_ports: count * (device.poe_ports ?? 0),
        core_count: core, core_ports: core ? [8, 16, 24, 48].find((ports) => ports >= count + 1) ?? 0 : 0,
        cabinet_count: count + core, router_count: c.remote_access ? 1 : 0,
        card_count: Number(c.apartments) * Number(c.cards_per_apartment),
        termination_count: endpoints * 2, patch_panel_count: Math.ceil(endpoints / 24), patch_count: endpoints * 2 + count * 2 + (c.remote_access ? 1 : 0),
        surge_count: Number(c.doors) * 2, project_count: 1,
        lock_psu_a: Math.max(2, Math.ceil((Number(c.lock_current_a) * 1.3 + (c.exit_type === "touchless" ? 0.1 : 0) + (c.external_reader ? 0.2 : 0)) * 10) / 10),
    };
}

export function intercomField(catalog: IntercomCatalog, field: CalculatorField, values: CalculatorValues): CalculatorField {
    if (!["door_station_id", "indoor_station_id", "switch_id"].includes(field.key)) return field;
    const ids = new Set(intercomDeviceOptions(catalog, field.key, values).map(([id]) => id));
    return { ...field, options: field.options.filter((option) => option.value === "auto" || ids.has(option.value)) };
}
