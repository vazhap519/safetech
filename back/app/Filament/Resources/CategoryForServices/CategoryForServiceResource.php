<?php

namespace App\Filament\Resources\CategoryForServices;

use App\Filament\Resources\CategoryForServices\Pages\CreateCategoryForService;
use App\Filament\Resources\CategoryForServices\Pages\EditCategoryForService;
use App\Filament\Resources\CategoryForServices\Pages\ListCategoryForServices;
use App\Filament\Resources\CategoryForServices\Schemas\CategoryForServiceForm;
use App\Filament\Support\CategoryTable;
use App\Filament\Support\NavigationGroup;
use App\Models\CategoryForService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryForServiceResource extends Resource
{
    protected static ?string $model = CategoryForService::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'სერვისის კატეგორიები';

    protected static ?string $modelLabel = 'სერვისის კატეგორია';

    protected static ?string $pluralModelLabel = 'სერვისის კატეგორიები';

    public static function form(Schema $schema): Schema
    {
        return CategoryForServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryTable::configure($table);
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
            'index' => ListCategoryForServices::route('/'),
            'create' => CreateCategoryForService::route('/create'),
            'edit' => EditCategoryForService::route('/{record}/edit'),
        ];
    }
}
