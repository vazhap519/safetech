import { expect, test } from "@playwright/test";
import type { APIRequestContext } from "@playwright/test";

import {
    getCompatibleComponents,
    initialCalculatorValues,
    withDerivedCalculatorValues,
    type CalculatorProfile,
} from "../src/lib/service-calculator";

const api = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";

async function profileFor(request: APIRequestContext, service: string) {
    const response = await request.get(
        `${api}/service-calculator/profiles?service=${encodeURIComponent(service)}&locale=en`,
    );
    expect(response.ok()).toBeTruthy();
    const payload = await response.json();
    const profiles = (Array.isArray(payload) ? payload : payload.data) as CalculatorProfile[];
    return profiles.find((profile) => profile.slug === service);
}

test("CCTV selection only exposes the matching camera, NVR and PoE class", async ({ request }) => {
    const profile = await profileFor(request, "security-camera-installation");
    expect(profile).toBeTruthy();

    const values = {
        ...initialCalculatorValues(profile!),
        camera_technology: "ip",
        camera_count: 12,
        resolution: "4mp",
    };
    const components = getCompatibleComponents(profile!, values, "small", "office", "standard");
    const keys = components.map(({ component }) => component.key);

    expect(keys).toContain("ip-camera-4mp");
    expect(keys).toContain("nvr-16ch-160mbps");
    expect(keys).toContain("poe-switch-16");
    expect(keys).not.toContain("ip-camera-2mp");
    expect(keys).not.toContain("nvr-8ch-80mbps");
});

test("low-voltage cable estimate uses area, points and reserve", async ({ request }) => {
    const profile = await profileFor(request, "low-voltage-cabling");
    expect(profile).toBeTruthy();

    const automatic = withDerivedCalculatorValues(profile!, {
        ...initialCalculatorValues(profile!),
        area_sqm: 400,
        network_points: 20,
        cable_estimation_mode: "auto",
    });
    expect(automatic.estimated_average_run_m).toBe(30);
    expect(automatic.estimated_cable_meters).toBe(672);
    expect(automatic.cable_meters).toBe(672);

    const manual = withDerivedCalculatorValues(profile!, {
        ...automatic,
        cable_estimation_mode: "manual",
        cable_meters: 725,
    });
    expect(manual.cable_meters).toBe(725);
});
