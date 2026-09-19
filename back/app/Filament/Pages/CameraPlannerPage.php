<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroup;
use Filament\Pages\Page;

class CameraPlannerPage extends Page
{
    protected static ?string $navigationLabel = 'კამერების პლანერი';

    protected static ?string $title = 'კამერების განლაგების პლანერი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.camera-planner';
}
