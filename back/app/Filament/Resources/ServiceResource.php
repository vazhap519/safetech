<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationLabel = 'სერვისები';

    protected static ?string $modelLabel = 'სერვისი';

    protected static ?string $pluralModelLabel = 'სერვისები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    /** @return array<int, mixed> */
    private static function cardSchema(bool $featured = false): array
    {
        return [
            TextInput::make('icon')->label('აიკონი'),
            TextInput::make('title')->label('სათაური')->required(),
            Textarea::make('description')->label('აღწერა')->required(),
            ...($featured ? [Toggle::make('featured')->label('გამორჩეული')] : []),
        ];
    }

    private static function localizedOptionRepeater(string $name, string $label): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                TextInput::make('value')
                    ->label('კოდი / მნიშვნელობა')
                    ->required()
                    ->helperText('მაგ: small, office, hotel'),
                TextInput::make('ka')->label('ქართული')->required(),
                TextInput::make('en')->label('ინგლისური'),
                TextInput::make('ru')->label('რუსული'),
                TextInput::make('one_time_price')
                    ->label('ერთჯერადი ფასის დამატება')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('₾'),
                TextInput::make('monthly_price')
                    ->label('ყოველთვიური ფასის დამატება')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('₾'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('სერვისის ძირითადი ინფორმაცია')
                ->schema([
                    TextInput::make('name')->label('სახელი')->required()->maxLength(255),
                    TextInput::make('slug')
                        ->label('URL კოდი')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('მაგ: networking'),
                    Select::make('category_for_service_id')
                        ->label('კატეგორია')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('eyebrow')->label('ზედა კატეგორია'),
                    TextInput::make('icon')->label('Material აიკონი')->default('settings')->required(),
                    TextInput::make('title')->label('მთავარი სათაური')->required(),
                    Textarea::make('description')->label('მოკლე აღწერა')->required()->rows(3),
                    Textarea::make('seo_description')
                        ->label('SEO აღწერა')
                        ->required()
                        ->rows(3)
                        ->maxLength(320),
                    SpatieMediaLibraryFileUpload::make('services')
                        ->label('მთავარი ფოტო')
                        ->collection('services')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->imagePreviewHeight('150'),
                    TagsInput::make('keywords')->label('SEO საკვანძო სიტყვები'),
                    TagsInput::make('highlights')->label('მთავარი უპირატესობები'),
                    TagsInput::make('industries')->label('ინდუსტრიები'),
                    TagsInput::make('brands')->label('ბრენდები'),
                ])
                ->columns(2),

            Section::make('კონტენტი და SEO 3 ენაზე')
                ->description('ზედა ძირითადი ველები რჩება ქართულ fallback-ად. აქედან KA/EN/RU ტექსტები ფრონტზე ავტომატურად წავა service.{slug} key-ებით.')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'სერვისის სახელი'),
                    ...LocalizedContentFields::inputs('eyebrow', 'ზედა კატეგორია'),
                    ...LocalizedContentFields::inputs('title', 'მთავარი სათაური'),
                    ...LocalizedContentFields::inputs('description', 'მოკლე აღწერა', textarea: true),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO სათაური'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO აღწერა', textarea: true),
                    ...LocalizedContentFields::inputs('card.title', 'ბარათის სათაური'),
                    ...LocalizedContentFields::inputs('card.description', 'ბარათის აღწერა', textarea: true),
                    LocalizedContentFields::customEntries('მაგ: benefit.0.title, process.0.description, keyword.0, highlight.0'),
                ])
                ->columns(3),

            Section::make('სერვისის ბლოკები')
                ->schema([
                    Repeater::make('benefits')
                        ->label('სარგებელი')
                        ->schema(self::cardSchema())
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('solutions')
                        ->label('გადაწყვეტილებები')
                        ->schema(self::cardSchema(true))
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('process')
                        ->label('სამუშაო პროცესი')
                        ->schema([
                            TextInput::make('title')->label('ეტაპი')->required(),
                            Textarea::make('description')->label('აღწერა')->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Textarea::make('overview')
                        ->label('მიმოხილვის JSON')
                        ->rule('json')
                        ->formatStateUsing(
                            fn ($state) => is_array($state)
                                ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                                : $state,
                        )
                        ->dehydrateStateUsing(
                            fn ($state) => is_string($state) ? json_decode($state, true) : $state,
                        )
                        ->helperText('სტრუქტურირებული overview ობიექტი JSON ფორმატში.'),
                    Textarea::make('warranty')->label('გარანტია'),
                    Textarea::make('sla')->label('SLA პირობები'),
                ]),

            Section::make('მოთხოვნის ფორმა და კალკულატორი')
                ->description('ეს არის სერვისის ფილტრების ერთადერთი წყარო: იგივე ველები გამოიყენება საკონტაქტო ფორმასა და ფასის კალკულატორში.')
                ->schema([
                    Toggle::make('lead_form.calculator_enabled')
                        ->label('კალკულატორში გამოჩენა')
                        ->default(true),
                    Select::make('lead_form.pricing.currency')
                        ->label('ვალუტა')
                        ->options(['GEL' => 'GEL (₾)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)'])
                        ->default('GEL')
                        ->required(),
                    TextInput::make('lead_form.pricing.base_price')
                        ->label('საწყისი ერთჯერადი ფასი')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.monthly_base_price')
                        ->label('საწყისი ყოველთვიური ფასი')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.minimum_price')
                        ->label('მინიმალური ერთჯერადი ფასი')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.project_size_label_ka')
                        ->label('პროექტის ზომის სათაური (KA)'),
                    TextInput::make('lead_form.project_size_label_en')
                        ->label('პროექტის ზომის სათაური (EN)'),
                    TextInput::make('lead_form.project_size_label_ru')
                        ->label('Название размера проекта (RU)'),
                    self::localizedOptionRepeater(
                        'lead_form.project_size_options',
                        'პროექტის ზომის ვარიანტები',
                    ),
                    TextInput::make('lead_form.property_type_label_ka')
                        ->label('ობიექტის ტიპის სათაური (KA)'),
                    TextInput::make('lead_form.property_type_label_en')
                        ->label('ობიექტის ტიპის სათაური (EN)'),
                    TextInput::make('lead_form.property_type_label_ru')
                        ->label('Название типа объекта (RU)'),
                    self::localizedOptionRepeater(
                        'lead_form.property_type_options',
                        'ობიექტის ტიპის ვარიანტები',
                    ),
                    Repeater::make('lead_form.extra_fields')
                        ->label('დამატებითი დინამიური ველები')
                        ->schema([
                            TextInput::make('key')
                                ->label('გასაღები')
                                ->required()
                                ->helperText('მაგ: router_count, camera_count'),
                            Select::make('type')
                                ->label('ველის ტიპი')
                                ->options([
                                    'text' => 'ტექსტი',
                                    'number' => 'რიცხვი',
                                    'textarea' => 'დიდი ტექსტი',
                                    'select' => 'არჩევანი',
                                    'checkbox' => 'ჩასართავი პარამეტრი',
                                ])
                                ->default('text')
                                ->required(),
                            Toggle::make('required')->label('სავალდებულო'),
                            TextInput::make('ka')->label('სათაური (KA)')->required(),
                            TextInput::make('en')->label('სათაური (EN)'),
                            TextInput::make('ru')->label('სათაური (RU)'),
                            TextInput::make('placeholder_ka')->label('მინიშნება (KA)'),
                            TextInput::make('placeholder_en')->label('მინიშნება (EN)'),
                            TextInput::make('placeholder_ru')->label('მინიშნება (RU)'),
                            TextInput::make('help_ka')->label('დახმარების ტექსტი (KA)'),
                            TextInput::make('help_en')->label('დახმარების ტექსტი (EN)'),
                            TextInput::make('help_ru')->label('Подсказка (RU)'),
                            TextInput::make('unit_ka')->label('ერთეული (KA)'),
                            TextInput::make('unit_en')->label('ერთეული (EN)'),
                            TextInput::make('unit_ru')->label('Единица (RU)'),
                            TextInput::make('min')->label('მინიმუმი')->numeric(),
                            TextInput::make('max')->label('მაქსიმუმი')->numeric(),
                            TextInput::make('step')->label('ბიჯი')->numeric(),
                            TextInput::make('default')->label('საწყისი მნიშვნელობა'),
                            TextInput::make('unit_price')
                                ->label('ერთეულის ერთჯერადი ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_unit_price')
                                ->label('ერთეულის ყოველთვიური ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('price_multiplier_field')
                                ->label('ფასის რაოდენობის ველი')
                                ->helperText('არჩევანის ფასი გამრავლდება მითითებული გასაღების რიცხვით მნიშვნელობაზე. მაგ: cable_meters.'),
                            Repeater::make('options')
                                ->label('არჩევანის ვარიანტები')
                                ->schema([
                                    TextInput::make('value')
                                        ->label('კოდი / მნიშვნელობა')
                                        ->required(),
                                    TextInput::make('ka')->label('ქართული')->required(),
                                    TextInput::make('en')->label('ინგლისური'),
                                    TextInput::make('ru')->label('რუსული'),
                                    TextInput::make('one_time_price')
                                        ->label('ერთჯერადი ფასი')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0),
                                    TextInput::make('monthly_price')
                                        ->label('ყოველთვიური ფასი')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0),
                                ])
                                ->columns(2)
                                ->default([])
                                ->collapsible()
                                ->reorderable()
                                ->visible(
                                    fn (Get $get): bool => ($get('type') ?? 'text') === 'select',
                                ),
                        ])
                        ->columns(2)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                    Repeater::make('lead_form.packages')
                        ->label('კალკულატორის პაკეტები')
                        ->schema([
                            TextInput::make('key')
                                ->label('გასაღები')
                                ->required()
                                ->helperText('მაგ: standard, business, managed'),
                            TextInput::make('title_ka')->label('სათაური (KA)')->required(),
                            TextInput::make('title_en')->label('სათაური (EN)'),
                            TextInput::make('title_ru')->label('Название (RU)'),
                            Textarea::make('description_ka')->label('აღწერა (KA)')->rows(2),
                            Textarea::make('description_en')->label('აღწერა (EN)')->rows(2),
                            Textarea::make('description_ru')->label('Описание (RU)')->rows(2),
                            TextInput::make('one_time_price')
                                ->label('ერთჯერადი ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_price')
                                ->label('ყოველთვიური ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            Toggle::make('recommended')->label('რეკომენდებული'),
                        ])
                        ->columns(3)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                    Textarea::make('lead_form.calculator_disclaimer_ka')
                        ->label('შენიშვნა (KA)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_en')
                        ->label('შენიშვნა (EN)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_ru')
                        ->label('Примечание (RU)')
                        ->rows(2),
                ])
                ->columns(3),

            Section::make('გამოქვეყნება')
                ->schema([
                    Toggle::make('is_published')->label('გამოქვეყნებულია')->default(false),
                    TextInput::make('sort_order')->label('რიგითობა')->numeric()->default(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->withAnalyticsSummary(),
            )
            ->columns([
                TextColumn::make('name')->label('სახელი')->searchable()->sortable(),
                TextColumn::make('slug')->label('URL კოდი')->searchable(),
                TextColumn::make('category.name')->label('კატეგორია')->sortable(),
                TextColumn::make('unique_viewers_count')
                    ->label('უნიკალური ნახვები')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_views_count')
                    ->label('სრული ნახვები')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('whatsapp_clicks_count')
                    ->label('WhatsApp-ზე გადასვლები')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')->label('გამოქვეყნებული')->boolean(),
                TextColumn::make('sort_order')->label('რიგი')->sortable(),
                TextColumn::make('updated_at')->label('განახლდა')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
