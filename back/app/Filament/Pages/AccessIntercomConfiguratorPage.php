<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroup;
use App\Support\Calculators\AccessIntercomConfigurator;
use Filament\Pages\Page;

class AccessIntercomConfiguratorPage extends Page
{
    protected static ?string $navigationLabel = 'RFID / დომოფონის კონფიგურატორი';

    protected static ?string $title = 'RFID / დაშვების და დომოფონის კონფიგურატორი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.access-intercom-configurator';

    public array $config = [
        'system' => 'access',
        'doors' => 1,
        'reader_sides' => 'entry',
        'reader_interface' => 'wiegand',
        'credential' => 'mifare',
        'lock_type' => 'maglock',
        'lock_current_a' => 0.5,
        'controller_current_a' => 0.3,
        'reader_current_a' => 0.12,
        'reserve_percent' => 30,
        'intercom_type' => 'ip',
        'apartments' => 1,
        'monitors_per_apartment' => 1,
    ];

    public function result(): array
    {
        return app(AccessIntercomConfigurator::class)->configure($this->config);
    }
}
