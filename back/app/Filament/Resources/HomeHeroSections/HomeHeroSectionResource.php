<?php

namespace App\Filament\Resources\HomeHeroSections;

use App\Filament\Resources\HomeHeroSections\Pages\CreateHomeHeroSection;
use App\Filament\Resources\HomeHeroSections\Pages\EditHomeHeroSection;
use App\Filament\Resources\HomeHeroSections\Pages\ListHomeHeroSections;
use App\Filament\Resources\HomeHeroSections\Schemas\HomeHeroSectionForm;
use App\Filament\Resources\HomeHeroSections\Tables\HomeHeroSectionsTable;
use App\Models\HomeHeroSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeHeroSectionResource extends Resource
{
    protected static ?string $model = HomeHeroSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HomeHeroSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeHeroSectionsTable::configure($table);
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
            'index' => ListHomeHeroSections::route('/'),
            'create' => CreateHomeHeroSection::route('/create'),
            'edit' => EditHomeHeroSection::route('/{record}/edit'),
        ];
    }
}
