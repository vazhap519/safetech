<?php

namespace App\Filament\Resources\HomeHowWorks;

use App\Filament\Resources\HomeHowWorks\Pages\CreateHomeHowWork;
use App\Filament\Resources\HomeHowWorks\Pages\EditHomeHowWork;
use App\Filament\Resources\HomeHowWorks\Pages\ListHomeHowWorks;
use App\Filament\Resources\HomeHowWorks\Schemas\HomeHowWorkForm;
use App\Filament\Resources\HomeHowWorks\Tables\HomeHowWorksTable;
use App\Models\HomeHowWork;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeHowWorkResource extends Resource
{
    protected static ?string $model = HomeHowWork::class;
    protected static string | UnitEnum | null $navigationGroup = 'Home';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HomeHowWorkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeHowWorksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeHowWorks::route('/'),
            'create' => CreateHomeHowWork::route('/create'),
            'edit' => EditHomeHowWork::route('/{record}/edit'),
        ];
    }
}
