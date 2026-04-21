<?php

namespace App\Filament\Resources\HomeWhyUs;

use App\Filament\Resources\HomeWhyUs\Pages\CreateHomeWhyUs;
use App\Filament\Resources\HomeWhyUs\Pages\EditHomeWhyUs;
use App\Filament\Resources\HomeWhyUs\Pages\ListHomeWhyUs;
use App\Filament\Resources\HomeWhyUs\Schemas\HomeWhyUsForm;
use App\Filament\Resources\HomeWhyUs\Tables\HomeWhyUsTable;
use App\Models\HomeWhyUs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeWhyUsResource extends Resource
{
    protected static ?string $model = HomeWhyUs::class;
    protected static string | UnitEnum | null $navigationGroup = 'Home';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HomeWhyUsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeWhyUsTable::configure($table);
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
            'index' => ListHomeWhyUs::route('/'),
            'create' => CreateHomeWhyUs::route('/create'),
            'edit' => EditHomeWhyUs::route('/{record}/edit'),
        ];
    }
}
