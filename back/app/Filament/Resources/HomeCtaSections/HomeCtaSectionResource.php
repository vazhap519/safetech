<?php

namespace App\Filament\Resources\HomeCtaSections;

use App\Filament\Resources\HomeCtaSections\Pages\CreateHomeCtaSection;
use App\Filament\Resources\HomeCtaSections\Pages\EditHomeCtaSection;
use App\Filament\Resources\HomeCtaSections\Pages\ListHomeCtaSections;
use App\Filament\Resources\HomeCtaSections\Schemas\HomeCtaSectionForm;
use App\Filament\Resources\HomeCtaSections\Tables\HomeCtaSectionsTable;
use App\Models\HomeCtaSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeCtaSectionResource extends Resource
{
    protected static ?string $model = HomeCtaSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HomeCtaSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeCtaSectionsTable::configure($table);
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
            'index' => ListHomeCtaSections::route('/'),
            'create' => CreateHomeCtaSection::route('/create'),
            'edit' => EditHomeCtaSection::route('/{record}/edit'),
        ];
    }
}
