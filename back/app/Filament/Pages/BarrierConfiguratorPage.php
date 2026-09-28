<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroup;
use App\Support\Calculators\BarrierConfigurator;
use App\Support\Calculators\BarrierDeviceCatalog;
use Filament\Pages\Page;

class BarrierConfiguratorPage extends Page
{
    protected static ?string $navigationLabel = 'შლაგბაუმის კონფიგურატორი';
    protected static ?string $title = 'შლაგბაუმი / LPR / UHF კონფიგურატორი';
    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 14;
    protected string $view = 'filament.pages.barrier-configurator';

    public ?string $lastCorrection = null;

    public array $config = [
        'lanes' => 1,
        'boom_type' => 'straight',
        'boom_length' => 4.5,
        'barrier_id' => 'zkteco-probg3000',
        'access_mode' => 'lpr',
        'lpr_camera_id' => 'hikvision-tcg406-e',
        'vehicle_trigger' => 'video',
        'uhf_reader_id' => 'zkteco-uhf5-pro',
        'controller_interface' => 'wiegand',
        'tag_count' => 20,
    ];

    public function mount(): void
    {
        $this->config = app(BarrierConfigurator::class)->normalize($this->config);
    }

    public function updatedConfig(mixed $value, ?string $key = null): void
    {
        $before = $this->config;
        $this->config = app(BarrierConfigurator::class)->normalize($this->config, $key);

        $changed = [];
        foreach ($this->config as $field => $normalized) {
            if (($before[$field] ?? null) !== $normalized && $field !== $key) {
                $changed[] = $field;
            }
        }

        $this->lastCorrection = $changed === []
            ? null
            : 'შეუსაბამო არჩევანი ავტომატურად გასწორდა: '.implode(', ', $changed);
    }

    public function barrierOptions(): array
    {
        return BarrierDeviceCatalog::options(BarrierDeviceCatalog::barriers());
    }

    public function lprOptions(): array
    {
        return BarrierDeviceCatalog::options(BarrierDeviceCatalog::lprCameras());
    }

    public function uhfOptions(): array
    {
        return BarrierDeviceCatalog::options(BarrierDeviceCatalog::uhfReaders());
    }

    public function result(): array
    {
        return app(BarrierConfigurator::class)->configure($this->config);
    }
}
