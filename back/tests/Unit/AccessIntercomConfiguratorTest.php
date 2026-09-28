<?php

namespace Tests\Unit;

use App\Support\Calculators\AccessIntercomConfigurator;
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

    public function test_hikvision_ip_intercom_family_stays_compatible_and_switch_scales(): void
    {
        $result = (new AccessIntercomConfigurator)->configure([
            'system' => 'intercom',
            'apartments' => 34,
            'monitors_per_apartment' => 1,
            'door_station_id' => 'hikvision-kv8113-wme1c',
            'indoor_station_id' => 'hikvision-kh6320-wte1',
            'switch_id' => 'hikvision-3e0105p-em-b',
            'lock_type' => 'strike',
        ]);

        $this->assertTrue($result['compatible']);
        $this->assertSame(34, $result['items'][1]['qty']);
        $this->assertGreaterThan(1, $result['items'][2]['qty']);
    }
}
