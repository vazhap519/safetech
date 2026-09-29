<?php

namespace App\Support\Calculators;

final class IntercomPlanner
{
    public function normalize(array $input): array
    {
        $c = array_replace(array_column(IntercomProfile::fields(), 'default', 'key'), $input);
        foreach (['apartments' => [1, 100], 'doors' => [1, 10], 'monitors_per_apartment' => [1, 6], 'cards_per_apartment' => [0, 10], 'cable_meters' => [0, 100000], 'lock_cable_meters' => [0, 100000], 'conduit_meters' => [0, 100000]] as $key => [$min, $max]) {
            $c[$key] = max($min, min($max, (int) $c[$key]));
        }
        $c['lock_current_a'] = max(0.1, min(5, (float) $c['lock_current_a']));
        foreach (IntercomProfile::fields() as $field) {
            if ($field['type'] === 'checkbox') {
                $c[$field['key']] = filter_var($c[$field['key']], FILTER_VALIDATE_BOOLEAN);
            }
        }
        $c['lock_type'] = in_array($c['lock_type'], ['maglock', 'strike', 'bolt'], true) ? $c['lock_type'] : 'maglock';
        $c['exit_type'] = $c['exit_type'] === 'touchless' ? 'touchless' : 'button';
        $options = $this->doorOptions($c);
        if (! isset($options[$c['door_station_id']])) {
            $c['door_station_id'] = array_key_first($options);
        }
        $options = $this->indoorOptions($c);
        if (! isset($options[$c['indoor_station_id']])) {
            $c['indoor_station_id'] = array_key_first($options);
        }
        $c['intercom_type'] = 'ip';
        $options = $this->switchOptions($c);
        if ($c['switch_id'] !== 'auto' && ! isset($options[$c['switch_id']])) {
            $c['switch_id'] = 'auto';
        }

        return $c;
    }

    public function doorOptions(array $c): array
    {
        return array_filter(AccessIntercomDeviceCatalog::doorStations(), fn (array $d): bool => $d['max_apartments'] >= (int) ($c['apartments'] ?? 1) && $d['max_doors'] >= (int) ($c['doors'] ?? 1));
    }

    public function indoorOptions(array $c): array
    {
        $door = AccessIntercomDeviceCatalog::doorStations()[$c['door_station_id'] ?? 'tvt-td-e2223'];

        return array_filter(AccessIntercomDeviceCatalog::indoorStations(), fn (array $d): bool => $d['ecosystem'] === $door['ecosystem'] && $d['max_per_apartment'] >= (int) ($c['monitors_per_apartment'] ?? 1));
    }

    public function switchOptions(array $c): array
    {
        $endpoints = (int) ($c['apartments'] ?? 1) * (int) ($c['monitors_per_apartment'] ?? 1) + (int) ($c['doors'] ?? 1);

        return array_filter(AccessIntercomDeviceCatalog::switches(), fn (array $d): bool => $this->switchCapacity($d) > 0 && ceil($endpoints / $this->switchCapacity($d)) <= IntercomProfile::catalog()['maxDistributionSwitches']);
    }

    public function switchCapacity(array $switch): int
    {
        $catalog = IntercomProfile::catalog();
        if ($switch['per_port_w'] < $catalog['poeAllocationW'] || $switch['dedicated_uplinks'] < 1) {
            return 0;
        }

        return min($switch['poe_ports'], (int) floor($switch['poe_budget_w'] / ($catalog['poeAllocationW'] * $catalog['poeReserve'])));
    }

    /** Flat derived quantities are also used by the public component rules. */
    public function quantities(array $input): array
    {
        $c = $this->normalize($input);
        $monitors = $c['apartments'] * $c['monitors_per_apartment'];
        $endpoints = $monitors + $c['doors'];
        $switches = $this->switchOptions($c);
        $switchId = $c['switch_id'];
        if ($switchId === 'auto') {
            $switchId = array_key_last($switches);
            foreach ($switches as $id => $switch) {
                if ($this->switchCapacity($switch) >= $endpoints) {
                    $switchId = $id;
                    break;
                }
            }
        }
        $switch = $switches[$switchId];
        $count = (int) ceil($endpoints / $this->switchCapacity($switch));
        $core = $count > 1 ? 1 : 0;
        $corePorts = 0;
        if ($core) {
            foreach ([8, 16, 24, 48] as $ports) {
                if ($ports >= $count + 1) {
                    $corePorts = $ports;
                    break;
                }
            }
        }
        $catalog = IntercomProfile::catalog();

        return array_merge($c, [
            'monitor_count' => $monitors, 'endpoint_count' => $endpoints,
            'resolved_switch_id' => $switchId, 'switch_count' => $count,
            'switch_usable_ports' => $this->switchCapacity($switch),
            'poe_required_w' => round($endpoints * $catalog['poeAllocationW'] * $catalog['poeReserve'], 2),
            'poe_available_w' => $count * $switch['poe_budget_w'],
            'poe_available_ports' => $count * $switch['poe_ports'],
            'core_count' => $core, 'core_ports' => $corePorts,
            'cabinet_count' => $count + $core,
            'router_count' => $c['remote_access'] ? 1 : 0,
            'card_count' => $c['apartments'] * $c['cards_per_apartment'],
            'termination_count' => $endpoints * 2,
            'patch_panel_count' => (int) ceil($endpoints / 24),
            'patch_count' => $endpoints * 2 + $count * 2 + ($c['remote_access'] ? 1 : 0),
            'surge_count' => $c['doors'] * 2,
            'project_count' => 1,
            'lock_psu_a' => max(2, ceil(($c['lock_current_a'] * 1.3 + ($c['exit_type'] === 'touchless' ? 0.1 : 0) + ($c['external_reader'] ? 0.2 : 0)) * 10) / 10),
        ]);
    }

    public function result(array $input): array
    {
        $q = $this->quantities($input);
        $items = [];
        foreach (IntercomProfile::components() as $component) {
            foreach ($component['rules'] as $rule) {
                $actual = $q[$rule['field']] ?? null;
                if ($rule['operator'] === 'truthy' ? ! $actual : (string) $actual !== $rule['value']) {
                    continue 2;
                }
            }
            $quantity = $q[$component['quantity_field']] ?? 0;
            if ($quantity <= 0) {
                continue;
            }
            $items[] = ['key' => $component['key'], 'group' => $component['category'], 'qty' => $quantity, 'item' => $component['title_ka'], 'why' => $component['description_ka']];
        }
        $door = AccessIntercomDeviceCatalog::doorStations()[$q['door_station_id']];
        $indoor = AccessIntercomDeviceCatalog::indoorStations()[$q['indoor_station_id']];
        $switch = AccessIntercomDeviceCatalog::switches()[$q['resolved_switch_id']];

        return [
            'system' => 'intercom', 'compatible' => true,
            'summary' => "{$q['apartments']} ბინა/აბონენტი · {$q['doors']} კარი · {$q['monitor_count']} მონიტორი",
            'selection' => ['გარე პანელი' => AccessIntercomDeviceCatalog::label($door), 'მონიტორი' => AccessIntercomDeviceCatalog::label($indoor), 'PoE' => AccessIntercomDeviceCatalog::label($switch)],
            'items' => $items, 'quantities' => $q, 'electrical' => null,
            'checks' => [
                "პანელის ტევადობა: {$door['max_apartments']} გამოძახების მისამართი; ამ არჩევანისთვის დასაშვები კარები: {$door['max_doors']}.",
                "PoE: {$q['endpoint_count']} მოწყობილობა; {$q['switch_count']} სვიჩი × {$q['switch_usable_ports']} სამუშაო პორტი (პორტებისა და კვების ბიუჯეტის ერთდროული შემოწმება).",
                "PoE მოთხოვნა: {$q['poe_required_w']}W (15.4W თითო პორტზე +20% რეზერვი); ხელმისაწვდომი: {$q['poe_available_w']}W. Uplink-ები ცალკე პორტებს იყენებს.",
                "აგრეგაცია: {$q['core_count']} სვიჩი, {$q['core_ports']} პორტი. საკეტის PSU: თითო კარზე მინ. {$q['lock_psu_a']}A @12V DC.",
            ],
            'warnings' => [
                'ეს არის კომპლექტაციის პროექტი. შეკვეთამდე შეამოწმეთ კონკრეტული firmware, საკეტის მექანიკა და რელეს დენი; ახალი TE და TD სერიები ავტომატურად არ ერევა.',
                'კვების ბლოკები, აკუმულატორი, UPS და კარადის ზომა შეირჩევა კვანძების დატვირთვისა და სასურველი ავტონომიის მიხედვით.',
                'Cat6, საკეტის კაბელი და გოფრა ითვლება მხოლოდ შეყვანილი სიგრძით. 100 მეტრზე გრძელ Ethernet ხაზზე დაიგეგმოს დამატებითი კვანძი ან ოპტიკა.',
                'ერთ ქსელში ყველა კარი ემსახურება არჩეულ ბინებს. სხვადასხვა იზოლირებული სადარბაზო ცალ-ცალკე დააკომპლექტეთ.',
            ],
        ];
    }
}
