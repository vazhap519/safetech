<?php

namespace Tests\Unit;

use App\Support\Calculators\BarrierConfigurator;
use PHPUnit\Framework\TestCase;

class BarrierConfiguratorTest extends TestCase
{
    public function test_wrong_barrier_is_replaced_for_six_meter_boom(): void
    {
        $config = (new BarrierConfigurator)->normalize([
            'boom_length' => 6,
            'boom_type' => 'straight',
            'barrier_id' => 'hikvision-tmg4b0-3m',
            'access_mode' => 'remote',
        ]);

        $this->assertNotSame('hikvision-tmg4b0-3m', $config['barrier_id']);
    }

    public function test_unsupported_lpr_trigger_is_replaced_by_supported_trigger(): void
    {
        $config = (new BarrierConfigurator)->normalize([
            'boom_length' => 4.5,
            'boom_type' => 'straight',
            'barrier_id' => 'zkteco-probg3000',
            'access_mode' => 'lpr',
            'lpr_camera_id' => 'zkteco-e-lprc500',
            'vehicle_trigger' => 'radar',
        ]);

        $this->assertSame('video', $config['vehicle_trigger']);
    }

    public function test_uhf_mode_keeps_supported_interface_and_generates_complete_set(): void
    {
        $result = (new BarrierConfigurator)->configure([
            'lanes' => 2,
            'boom_length' => 4.5,
            'boom_type' => 'straight',
            'barrier_id' => 'zkteco-probg3000',
            'access_mode' => 'uhf',
            'uhf_reader_id' => 'zkteco-uhf5-pro',
            'controller_interface' => 'rs485',
            'tag_count' => 40,
        ]);

        $this->assertSame('RS485', $result['selection']['interface']);
        $this->assertSame(2, $result['items'][0]['qty']);
        $this->assertSame(40, collect($result['items'])->firstWhere('group', 'UHF Tag')['qty']);
    }
}
