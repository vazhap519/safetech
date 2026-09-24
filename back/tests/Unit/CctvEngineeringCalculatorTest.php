<?php

namespace Tests\Unit;

use App\Support\Calculators\CctvEngineeringCalculator;
use PHPUnit\Framework\TestCase;

class CctvEngineeringCalculatorTest extends TestCase
{
    public function test_continuous_recording_uses_actual_video_bitrate_and_decimal_tb(): void
    {
        $data = (new CctvEngineeringCalculator)->calculate([
            'days' => 14,
            'reserve_percent' => 20,
            'groups' => [[
                'count' => 6, 'megapixels' => 2,
                'fps' => 15, 'bitrate_mbps' => 2.5, 'audio_mbps' => 0,
                'mode' => 'continuous', 'camera_watts' => 8, 'power_type' => 'poe',
            ]],
        ]);

        $this->assertSame(6, $data['camera_count']);
        $this->assertEqualsWithDelta(2.268, $data['storage_raw_tb'], 0.001);
        $this->assertEqualsWithDelta(2.722, $data['storage_required_tb'], 0.002);
        $this->assertSame(4, $data['suggested_single_disk_tb']);
        $this->assertSame(48.0, $data['poe_load_watts']);
    }

    public function test_motion_duty_is_only_applied_to_disk_not_to_peak_network_or_power(): void
    {
        $base = ['days' => 10, 'reserve_percent' => 0, 'groups' => [[
            'count' => 2, 'bitrate_mbps' => 4, 'mode' => 'motion',
            'hours_per_day' => 12, 'motion_percent' => 25,
            'camera_watts' => 10, 'power_type' => 'poe',
        ]]];
        $result = (new CctvEngineeringCalculator)->calculate($base);
        $this->assertEqualsWithDelta(0.108, $result['storage_raw_tb'], 0.001);
        $this->assertSame(8.0, $result['video_peak_mbps']);
        $this->assertSame(20.0, $result['poe_load_watts']);
    }

    public function test_no_camera_results_in_no_camera_storage_or_poe_load(): void
    {
        $result = (new CctvEngineeringCalculator)->calculate(['groups' => []]);
        $this->assertSame(0, $result['camera_count']);
        $this->assertSame(0.0, $result['storage_required_tb']);
        $this->assertSame(0.0, $result['poe_load_watts']);
    }

    public function test_megapixel_input_drives_the_automatic_bitrate_estimate(): void
    {
        $result = (new CctvEngineeringCalculator)->calculate([
            'groups' => [[
                'count' => 1,
                'megapixels' => 8,
                'fps' => 15,
                'codec' => 'h265',
                'bitrate_mbps' => 0,
            ]],
        ]);

        $this->assertSame(8.0, $result['groups'][0]['megapixels']);
        $this->assertSame(7.2, $result['groups'][0]['video_mbps_each']);
        $this->assertSame('estimated', $result['groups'][0]['bitrate_source']);
    }

    public function test_legacy_width_and_height_payloads_remain_compatible(): void
    {
        $result = (new CctvEngineeringCalculator)->calculate([
            'groups' => [[
                'count' => 1,
                'width' => 2560,
                'height' => 1440,
                'fps' => 15,
                'codec' => 'h265',
                'bitrate_mbps' => 0,
            ]],
        ]);

        $this->assertSame(3.69, $result['groups'][0]['megapixels']);
        $this->assertSame(3.32, $result['groups'][0]['video_mbps_each']);
    }

    public function test_megapixel_input_is_normalized_to_an_even_option(): void
    {
        $result = (new CctvEngineeringCalculator)->calculate([
            'groups' => [[
                'count' => 1,
                'megapixels' => 3,
                'bitrate_mbps' => 1,
            ]],
        ]);

        $this->assertSame(4.0, $result['groups'][0]['megapixels']);
    }
}
