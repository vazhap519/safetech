<?php

namespace App\Filament\Resources\HomeTrusts;

use App\Filament\Resources\HomeTrusts\Pages\CreateHomeTrust;
use App\Filament\Resources\HomeTrusts\Pages\EditHomeTrust;
use App\Filament\Resources\HomeTrusts\Pages\ListHomeTrusts;
use App\Filament\Resources\HomeTrusts\Schemas\HomeTrustForm;
use App\Filament\Resources\HomeTrusts\Tables\HomeTrustsTable;
use App\Models\HomeTrust;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeTrustResource extends Resource
{
    protected static ?string $model = HomeTrust::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Home';

    public static function form(Schema $schema): Schema
    {
        return HomeTrustForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeTrustsTable::configure($table);
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
            'index' => ListHomeTrusts::route('/'),
            'create' => CreateHomeTrust::route('/create'),
            'edit' => EditHomeTrust::route('/{record}/edit'),
        ];
    }
}
