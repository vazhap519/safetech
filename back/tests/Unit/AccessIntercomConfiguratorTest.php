<?php

namespace Tests\Unit;

use App\Support\Calculators\AccessIntercomConfigurator;
use App\Support\Calculators\IntercomPlanner;
use PHPUnit\Framework\TestCase;

class AccessIntercomConfiguratorTest extends TestCase
{
    public function test_incompatible_wiegand_controller_is_replaced_for_osdp_reader_topology(): void
    {
        $config = (new AccessIntercomConfigurator)->normalize([
            'system' => 'access',
            'doors' => 4,
            'reader_sides' => 'entry_exit',
            'reader_interface' => 'osdp',
            'credential' => 'mifare',
            'controller_id' => 'zkteco-c3-400',
            'reader_id' => 'zkteco-kr500-m',
        ]);

        $this->assertSame('hikvision-k2604t', $config['controller_id']);
        $this->assertSame('hikvision-k1108m', $config['reader_id']);
    }

    public function test_selecting_em_reader_synchronizes_credential_and_keeps_compatible_controller(): void
    {
        $config = (new AccessIntercomConfigurator)->normalize([
            'system' => 'access',
            'doors' => 2,
            'reader_sides' => 'entry',
            'reader_interface' => 'wiegand',
            'credential' => 'mifare',
            'controller_id' => 'zkteco-c3-200',
            'reader_id' => 'zkteco-kr500-e',
        ], 'reader_id');

        $this->assertSame('em', $config['credential']);
        $this->assertSame('zkteco-kr500-e', $config['reader_id']);
        $this->assertSame('zkteco-c3-200', $config['controller_id']);
    }

    public function test_four_door_two_way_wiegand_avoids_c3_400_reader_port_limit(): void
    {
        $config = (new AccessIntercomConfigurator)->normalize([
            'system' => 'access',
            'doors' => 4,
            'reader_sides' => 'entry_exit',
            'reader_interface' => 'wiegand',
            'credential' => 'mifare',
            'controller_id' => 'zkteco-c3-400',
            'reader_id' => 'zkteco-kr500-m',
        ]);

        $this->assertNotSame('zkteco-c3-400', $config['controller_id']);
        $result = (new AccessIntercomConfigurator)->configure($config);
        $this->assertSame(8, $result['electrical']['readers']);
        $this->assertTrue($result['compatible']);
    }

    public function test_multifamily_scope_replaces_single_subscriber_panels(): void
    {
        $calculator = new AccessIntercomConfigurator;
        $input = ['system' => 'intercom', 'apartments' => 34, 'doors' => 3, 'door_station_id' => 'hikvision-kv8113-wme1c', 'indoor_station_id' => 'hikvision-kh6320-wte1'];
        $config = $calculator->normalize($input);
        $this->assertSame('tvt-td-e2223', $config['door_station_id']);
        $this->assertSame('tvt-td-e2137', $config['indoor_station_id']);
        $result = $calculator->configure($config);
        $quantities = array_column($result['items'], 'qty', 'key');
        $this->assertSame(34, $quantities['tvt-td-e2137']);
        $this->assertSame(3, $quantities['tvt-td-e2223']);
        $this->assertSame(3, $quantities['lock-maglock']);
    }

    public function test_bounds_boolean_options_and_model_changes_are_normalized(): void
    {
        $planner = new IntercomPlanner;
        $c = $planner->normalize(['apartments' => 1000, 'doors' => -1, 'monitors_per_apartment' => 99, 'switch_id' => 'passive-poe', 'backup_power' => 'false']);
        $this->assertSame(100, $c['apartments']);
        $this->assertSame(1, $c['doors']);
        $this->assertSame(6, $c['monitors_per_apartment']);
        $this->assertSame('auto', $c['switch_id']);
        $this->assertFalse($c['backup_power']);
        $c = $planner->normalize(['apartments' => 8, 'doors' => 1, 'door_station_id' => 'tvt-te-vd1108']);
        $this->assertSame('tvt-te-vh1104', $c['indoor_station_id']);
        $c = $planner->normalize(array_replace($c, ['apartments' => 9]));
        $this->assertSame('tvt-td-e2223', $c['door_station_id']);
        $this->assertSame('tvt-td-e2137', $c['indoor_station_id']);
    }

    public function test_every_requested_scope_has_enough_ports_power_and_core_capacity(): void
    {
        $planner = new IntercomPlanner;
        foreach ([1, 8, 9, 34, 72, 100] as $apartments) {
            foreach ([1, 10] as $doors) {
                foreach ([1, 6] as $monitors) {
                    foreach (['auto', 'generic-standard-poe-4', 'generic-standard-poe-24'] as $switch) {
                        $q = $planner->quantities(['apartments' => $apartments, 'doors' => $doors, 'monitors_per_apartment' => $monitors, 'switch_id' => $switch]);
                        $this->assertSame($apartments * $monitors + $doors, $q['endpoint_count']);
                        $this->assertGreaterThanOrEqual($q['endpoint_count'], $q['poe_available_ports']);
                        $this->assertGreaterThanOrEqual($q['poe_required_w'], $q['poe_available_w']);
                        $this->assertLessThanOrEqual(47, $q['switch_count']);
                        if ($q['core_count']) {
                            $this->assertGreaterThanOrEqual($q['switch_count'] + 1, $q['core_ports']);
                        }
                    }
                }
            }
        }
    }

    public function test_accessories_follow_scope_and_optional_parts_can_be_removed(): void
    {
        $planner = new IntercomPlanner;
        $r = $planner->result(['apartments' => 100, 'doors' => 10, 'cards_per_apartment' => 3, 'monitors_per_apartment' => 2, 'cable_meters' => 1500, 'backup_power' => false, 'lock_type' => 'strike', 'sd_cards' => true]);
        $q = array_column($r['items'], 'qty', 'key');
        $this->assertSame(200, $q['tvt-td-e2137']);
        $this->assertSame(10, $q['lock-strike']);
        $this->assertSame(10, $q['lock-psu']);
        $this->assertSame(300, $q['mifare-tags']);
        $this->assertSame(200, $q['microsd']);
        $this->assertSame(1500, $q['cat6']);
        $this->assertArrayNotHasKey('lock-battery', $q);
        $this->assertArrayNotHasKey('network-ups', $q);
        $this->assertArrayNotHasKey('emergency-release', $q);
        $this->assertArrayNotHasKey('lock-maglock', $q);
    }
}
