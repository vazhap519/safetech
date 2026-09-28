<?php

namespace Tests\Unit;

use App\Support\Calculators\AccessIntercomConfigurator;
use PHPUnit\Framework\TestCase;

class AccessIntercomConfiguratorTest extends TestCase
{
    public function test_two_door_access_system_recommends_matching_controller_readers_and_psu(): void
    {
        $result = (new AccessIntercomConfigurator)->configure([
            'system' => 'access',
            'doors' => 2,
            'reader_sides' => 'entry_exit',
            'reader_interface' => 'wiegand',
            'credential' => 'mifare',
            'lock_type' => 'maglock',
            'lock_current_a' => 0.5,
            'reader_current_a' => 0.12,
            'controller_current_a' => 0.3,
            'reserve_percent' => 30,
        ]);

        $this->assertSame(4, $result['electrical']['readers']);
        $this->assertSame(1.8, $result['electrical']['estimated_load_a']);
        $this->assertSame(2.4, $result['electrical']['recommended_psu_a']);
        $this->assertStringContainsString('C3-200', $result['items'][0]['item']);
    }

    public function test_ip_intercom_builds_monitor_and_poe_requirements(): void
    {
        $result = (new AccessIntercomConfigurator)->configure([
            'system' => 'intercom',
            'intercom_type' => 'ip',
            'apartments' => 34,
            'monitors_per_apartment' => 1,
            'lock_type' => 'strike',
        ]);

        $this->assertSame(34, $result['items'][1]['qty']);
        $this->assertSame('IP indoor monitor', $result['items'][1]['item']);
        $this->assertStringContainsString('PoE switch', $result['items'][2]['item']);
        $this->assertNotEmpty($result['warnings']);
    }
}
