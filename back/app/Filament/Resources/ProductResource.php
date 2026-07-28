<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Filament\Support\StableSlug;
use App\Filament\Support\StructuredDataJsonField;
use App\Models\Product;
use App\Models\ProductFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Products';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Main product information')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(StableSlug::syncOnCreate()),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Generated automatically from the name, but still editable.'),
                    Select::make('product_category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->minValue(0)
                        ->nullable()
                        ->helperText('Leave empty to show the localized "Contact us for pricing" message.'),
                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'GEL' => 'GEL',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                        ])
                        ->default('GEL')
                        ->required(),
                    TextInput::make('image_alt')
                        ->label('Image alt text'),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0),
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label('Main image')
                        ->collection('cover')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->helperText('Uploaded cover images are converted to WebP automatically for frontend delivery.'),
                    SpatieMediaLibraryFileUpload::make('gallery_uploads')
                        ->label('Gallery')
                        ->collection('gallery')
                        ->conversion('webp')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->helperText('You can select and upload multiple images at once. Each gallery image is converted to WebP automatically.'),
                    RichEditor::make('short_description')
                        ->label('Short description')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'bulletList',
                            'orderedList',
                            'link',
                            'undo',
                            'redo',
                        ]),
                    RichEditor::make('description')
                        ->label('Long description / details')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Product filters')
                ->description('Choose dynamic filter groups and option values that should match this product on the shop page.')
                ->schema([
                    Repeater::make('filter_values')
                        ->label('Assigned filter values')
                        ->schema([
                            Select::make('filter_slug')
                                ->label('Filter group')
                                ->options(fn (): array => ProductFilter::query()
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                                    ->pluck('name', 'slug')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),
                            Select::make('option_slugs')
                                ->label('Options')
                                ->multiple()
                                ->options(function (Get $get): array {
                                    $filterSlug = $get('filter_slug');

                                    if (! is_string($filterSlug) || trim($filterSlug) === '') {
                                        return [];
                                    }

                                    $filter = ProductFilter::query()->where('slug', $filterSlug)->first();

                                    if (! $filter) {
                                        return [];
                                    }

                                    return $filter->resolvedOptions()
                                        ->pluck('label', 'slug')
                                        ->all();
                                })
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                ]),
            Section::make('Translations and SEO (KA/EN/RU)')
                ->description('The main fields above remain fallback content. These translations are consumed automatically on the frontend.')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'Product name'),
                    ...LocalizedContentFields::inputs('shortDescription', 'Short description', textarea: true, rows: 4),
                    ...LocalizedContentFields::inputs('description', 'Long description', textarea: true, rows: 6),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO title'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO description', textarea: true, rows: 4),
                    ...LocalizedContentFields::inputs('ogTitle', 'Open Graph title'),
                    ...LocalizedContentFields::inputs('ogDescription', 'Open Graph description', textarea: true, rows: 4),
                    ...LocalizedContentFields::inputs('imageAlt', 'Image alt'),
                    LocalizedContentFields::customEntries('Examples: badge.0, priceLabel, specification.0.title'),
                ])
                ->columns(3),
            Section::make('Advanced SEO')
                ->schema([
                    TextInput::make('seo.title')
                        ->label('SEO title')
                        ->maxLength(180),
                    TextInput::make('seo.og_title')
                        ->label('Open Graph title')
                        ->maxLength(180),
                    TagsInput::make('seo.keywords')
                        ->label('SEO keywords'),
                    TextInput::make('seo.canonical')
                        ->label('Canonical URL')
                        ->helperText('Optional absolute canonical override.'),
                    RichEditor::make('seo.description')
                        ->label('SEO description')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'undo',
                            'redo',
                        ]),
                    RichEditor::make('seo.og_description')
                        ->label('Open Graph description')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'undo',
                            'redo',
                        ]),
                    StructuredDataJsonField::make(
                        'Leave empty to let the frontend generate the default Product schema automatically.',
                        'seo.schema',
                    ),
                    Toggle::make('seo.noindex')
                        ->label('Do not index this product')
                        ->default(false),
                ])
                ->columns(2),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(false),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money(fn (Product $record): string => $record->currency ?: 'GEL', divideBy: 1),
                IconColumn::make('is_published')->label('Published')->boolean(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
