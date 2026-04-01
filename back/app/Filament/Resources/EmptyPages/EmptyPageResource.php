<?php

namespace App\Filament\Resources\EmptyPages;

use App\Filament\Resources\EmptyPages\Pages\CreateEmptyPage;
use App\Filament\Resources\EmptyPages\Pages\EditEmptyPage;
use App\Filament\Resources\EmptyPages\Pages\ListEmptyPages;
use App\Filament\Resources\EmptyPages\Schemas\EmptyPageForm;
use App\Filament\Resources\EmptyPages\Tables\EmptyPagesTable;
use App\Models\EmptyPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmptyPageResource extends Resource
{
    protected static ?string $model = EmptyPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmptyPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmptyPagesTable::configure($table);
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
            'index' => ListEmptyPages::route('/'),
            'create' => CreateEmptyPage::route('/create'),
            'edit' => EditEmptyPage::route('/{record}/edit'),
        ];
    }
}
