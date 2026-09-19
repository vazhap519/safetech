<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroup;
use App\Support\Calculators\CctvEngineeringCalculator;
use Filament\Pages\Page;

class CctvEngineeringPage extends Page
{
    protected static ?string $navigationLabel = 'CCTV • HDD / UPS კალკულატორი';

    protected static ?string $title = 'CCTV საინჟინრო კალკულატორი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.cctv-engineering';

    public array $settings = [
        'days' => 14,
        'reserve_percent' => 20,
        'disk_usable_percent' => 100,
        'runtime_hours' => 2,
        'nvr_watts' => 25,
        'switch_watts' => 10,
        'other_watts' => 0,
        'poe_efficiency_percent' => 88,
        'headroom_percent' => 25,
        'ups_power_factor' => 0.7,
        'battery_voltage' => 12,
        'battery_dod_percent' => 80,
        'inverter_efficiency_percent' => 85,
    ];

    public array $groups = [[
        'count' => 4, 'width' => 2560, 'height' => 1440, 'fps' => 15,
        'codec' => 'h265', 'bitrate_mbps' => 4, 'audio_mbps' => 0,
        'mode' => 'continuous', 'hours_per_day' => 24, 'motion_percent' => 35,
        'camera_watts' => 8, 'power_type' => 'poe', 'voltage' => 48,
    ]];

    public function addGroup(): void
    {
        if (count($this->groups) < 32) {
            $this->groups[] = [
                'count' => 1, 'width' => 1920, 'height' => 1080, 'fps' => 15,
                'codec' => 'h265', 'bitrate_mbps' => 2.5, 'audio_mbps' => 0,
                'mode' => 'continuous', 'hours_per_day' => 24, 'motion_percent' => 35,
                'camera_watts' => 8, 'power_type' => 'poe', 'voltage' => 48,
            ];
        }
    }

    public function removeGroup(int $index): void
    {
        if (count($this->groups) > 1) {
            array_splice($this->groups, $index, 1);
        }
    }

    public function engineeringResult(): array
    {
        return app(CctvEngineeringCalculator::class)->calculate(
            array_merge($this->settings, ['groups' => $this->groups]),
        );
    }
}
