<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteCatalogItemResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\QuoteCatalogItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteCatalogItemResource extends Resource
{
    protected static ?string $model = QuoteCatalogItem::class;

    protected static ?string $navigationLabel = 'Quote Catalog / თვითღირებულებები';

    protected static ?string $modelLabel = 'კატალოგის პოზიცია';

    protected static ?string $pluralModelLabel = 'Quote Catalog';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('კომპონენტი')->schema([
                Select::make('service_id')
                    ->label('სერვისი')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('component_key')
                    ->label('კომპონენტის გასაღები')
                    ->helperText('უნდა ემთხვეოდეს სერვისის კონფიგურატორის key-ს.')
                    ->required(),
                TextInput::make('name')->label('დასახელება')->required(),
                Select::make('category')->label('კატეგორია')->options([
                    'camera' => 'კამერა',
                    'recorder' => 'NVR / DVR',
                    'storage' => 'დისკი / საცავი',
                    'network' => 'ქსელი / PoE',
                    'cabling' => 'კაბელი',
                    'power' => 'UPS / კვება',
                    'intercom' => 'დომოფონი',
                    'lock' => 'საკეტი',
                    'accessory' => 'აქსესუარი',
                    'server' => 'სერვერი',
                    'labor' => 'სამუშაო',
                    'other' => 'სხვა',
                ])->default('other')->required(),
                Toggle::make('is_active')->label('აქტიური')->default(true),
            ])->columns(2),

            Section::make('მომწოდებელი და მოდელი')->schema([
                TextInput::make('brand')->label('ბრენდი'),
                TextInput::make('model')->label('მოდელი / SKU'),
                TextInput::make('supplier')->label('მომწოდებელი'),
                TextInput::make('warranty_months')
                    ->label('გარანტია (თვე)')
                    ->numeric()
                    ->minValue(0),
            ])->columns(2),

            Section::make('ფასი და მოგება')
                ->description('თუ გასაყიდი ფასი ცარიელია, Quote Engine გამოიყენებს: შესყიდვის ფასი × (1 + ფასნამატი / 100).')
                ->schema([
                    TextInput::make('purchase_price')
                        ->label('შესყიდვის ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('markup_percentage')
                        ->label('ფასნამატი')
                        ->suffix('%')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(1000)
                        ->default(60)
                        ->required(),
                    TextInput::make('sale_price')
                        ->label('ფიქსირებული გასაყიდი ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->minValue(0)
                        ->helperText('დატოვე ცარიელი, თუ ფასი ავტომატურად უნდა დაითვალოს ფასნამატიდან.'),
                    Textarea::make('notes')->label('შიდა შენიშვნა')->rows(3),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.name')->label('სერვისი')->searchable()->sortable(),
                TextColumn::make('name')->label('კომპონენტი')->searchable()->sortable(),
                TextColumn::make('brand')->label('ბრენდი')->toggleable(),
                TextColumn::make('model')->label('მოდელი')->searchable()->toggleable(),
                TextColumn::make('supplier')->label('მომწოდებელი')->searchable()->toggleable(),
                TextColumn::make('purchase_price')->label('შესყიდვა')->money('GEL')->sortable(),
                TextColumn::make('markup_percentage')->label('ფასნამატი')->suffix('%')->sortable(),
                TextColumn::make('sale_price')->label('ფიქს. გაყიდვა')->money('GEL')->sortable(),
                IconColumn::make('is_active')->label('აქტიური')->boolean(),
            ])
            ->filters([
                SelectFilter::make('service_id')->label('სერვისი')->relationship('service', 'name'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteCatalogItems::route('/'),
            'create' => Pages\CreateQuoteCatalogItem::route('/create'),
            'edit' => Pages\EditQuoteCatalogItem::route('/{record}/edit'),
        ];
    }
}
