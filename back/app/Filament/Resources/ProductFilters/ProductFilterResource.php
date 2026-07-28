<?php

namespace App\Filament\Resources\ProductFilters;

use App\Filament\Resources\ProductFilters\Pages\CreateProductFilter;
use App\Filament\Resources\ProductFilters\Pages\EditProductFilter;
use App\Filament\Resources\ProductFilters\Pages\ListProductFilters;
use App\Filament\Resources\ProductFilters\Schemas\ProductFilterForm;
use App\Filament\Support\NavigationGroup;
use App\Models\ProductFilter;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProductFilterResource extends Resource
{
    protected static ?string $model = ProductFilter::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Product Filters';

    protected static ?string $modelLabel = 'Product Filter';

    protected static ?string $pluralModelLabel = 'Product Filters';

    public static function form(Schema $schema): Schema
    {
        return ProductFilterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable()->sortable()->copyable(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductFilters::route('/'),
            'create' => CreateProductFilter::route('/create'),
            'edit' => EditProductFilter::route('/{record}/edit'),
        ];
    }
}
