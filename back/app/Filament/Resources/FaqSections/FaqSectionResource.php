<?php

namespace App\Filament\Resources\FaqSections;

use App\Filament\Resources\FaqSections\Pages\CreateFaqSection;
use App\Filament\Resources\FaqSections\Pages\EditFaqSection;
use App\Filament\Resources\FaqSections\Pages\ListFaqSections;
use App\Filament\Resources\FaqSections\Schemas\FaqSectionForm;
use App\Filament\Resources\FaqSections\Tables\FaqSectionsTable;
use App\Models\FaqSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FaqSectionResource extends Resource
{
    protected static ?string $model = FaqSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FaqSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaqSectionsTable::configure($table);
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
            'index' => ListFaqSections::route('/'),
            'create' => CreateFaqSection::route('/create'),
            'edit' => EditFaqSection::route('/{record}/edit'),
        ];
    }
}
