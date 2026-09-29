<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroup;
use App\Support\Calculators\AccessIntercomConfigurator;
use App\Support\Calculators\AccessIntercomDeviceCatalog;
use App\Support\Calculators\IntercomPlanner;
use App\Support\Calculators\IntercomProfile;
use Filament\Pages\Page;

class AccessIntercomConfiguratorPage extends Page
{
    protected static ?string $navigationLabel = 'RFID / დომოფონის კონფიგურატორი';

    protected static ?string $title = 'RFID / დაშვების და დომოფონის კონფიგურატორი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.access-intercom-configurator';

    public ?string $lastCorrection = null;

    public bool $scopeConfirmed = false;

    public array $config = [
        'system' => 'intercom',
        'doors' => 1,
        'reader_sides' => 'entry',
        'reader_interface' => 'wiegand',
        'credential' => 'mifare',
        'controller_id' => 'zkteco-c3-100',
        'reader_id' => 'zkteco-kr500-m',
        'lock_type' => 'maglock',
        'lock_current_a' => 0.5,
        'controller_current_a' => 0.3,
        'reader_current_a' => 0.12,
        'reserve_percent' => 30,
        'intercom_type' => 'ip',
        'apartments' => 1,
        'monitors_per_apartment' => 1,
        'door_station_id' => 'tvt-td-e2223',
        'indoor_station_id' => 'tvt-td-e2137',
        'switch_id' => 'auto',
    ];

    public function mount(): void
    {
        $this->config = app(AccessIntercomConfigurator::class)->normalize($this->config);
    }

    public function updatedConfig(mixed $value, ?string $key = null): void
    {
        if (in_array($key, ['apartments', 'doors', 'system'], true)) {
            $this->scopeConfirmed = false;
        }
        $before = $this->config;
        $this->config = app(AccessIntercomConfigurator::class)->normalize($this->config, $key);

        $changed = [];
        foreach ($this->config as $field => $normalized) {
            if (($before[$field] ?? null) !== $normalized && $field !== $key) {
                $changed[] = $field;
            }
        }

        $this->lastCorrection = $changed === []
            ? null
            : 'მოწყობილობები არჩეული მასშტაბისა და სერიის მიხედვით განახლდა.';
    }

    public function controllerOptions(): array
    {
        return AccessIntercomDeviceCatalog::options(AccessIntercomDeviceCatalog::controllers());
    }

    public function readerOptions(): array
    {
        return AccessIntercomDeviceCatalog::options(AccessIntercomDeviceCatalog::readers());
    }

    public function showDevices(): void
    {
        $this->config = app(AccessIntercomConfigurator::class)->normalize($this->config);
        $this->scopeConfirmed = true;
    }

    public function intercomFields(): array
    {
        return IntercomProfile::fields();
    }

    public function intercomFieldOptions(array $field): array
    {
        $planner = new IntercomPlanner;
        $devices = match ($field['key']) {
            'door_station_id' => $planner->doorOptions($this->config),
            'indoor_station_id' => $planner->indoorOptions($this->config),
            'switch_id' => $planner->switchOptions($this->config),
            default => null,
        };
        if ($devices === null) {
            return $field['options'] ?? [];
        }

        return array_values(array_filter($field['options'], fn (array $option): bool => $option['value'] === 'auto' || isset($devices[$option['value']])));
    }

    public function doorStationOptions(): array
    {
        return AccessIntercomDeviceCatalog::options(AccessIntercomDeviceCatalog::doorStations());
    }

    public function indoorStationOptions(): array
    {
        return AccessIntercomDeviceCatalog::options(AccessIntercomDeviceCatalog::indoorStations());
    }

    public function switchOptions(): array
    {
        return AccessIntercomDeviceCatalog::options(AccessIntercomDeviceCatalog::switches());
    }

    public function result(): array
    {
        return app(AccessIntercomConfigurator::class)->configure($this->config);
    }
}
