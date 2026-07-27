<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\AdminIconOptions;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Filament\Support\StableSlug;
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

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    /** @return array<int, mixed> */
    private static function cardSchema(bool $featured = false): array
    {
        return [
            Select::make('icon')
                ->label('Icon')
                ->options(AdminIconOptions::content())
                ->searchable()
                ->preload(),
            TextInput::make('title')->label('Title')->required(),
            Textarea::make('description')->label('Description')->required(),
            ...($featured ? [Toggle::make('featured')->label('Featured')] : []),
        ];
    }

    private static function localizedOptionRepeater(string $name, string $label): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                TextInput::make('value')
                    ->label('Value')
                    ->required()
                    ->helperText('Example: small, office, hotel'),
                TextInput::make('ka')->label('Georgian')->required(),
                TextInput::make('en')->label('English'),
                TextInput::make('ru')->label('Russian'),
                TextInput::make('one_time_price')
                    ->label('One-time price delta')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('GEL'),
                TextInput::make('monthly_price')
                    ->label('Monthly price delta')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->suffix('GEL'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Main service information')
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
                    Select::make('category_for_service_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('eyebrow')->label('Eyebrow'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options(AdminIconOptions::content())
                        ->searchable()
                        ->preload()
                        ->default('settings')
                        ->required(),
                    TextInput::make('title')->label('Headline')->required(),
                    Textarea::make('description')
                        ->label('Short description')
                        ->required()
                        ->rows(3),
                    Textarea::make('seo_description')
                        ->label('SEO description')
                        ->required()
                        ->rows(3)
                        ->maxLength(320),
                    SpatieMediaLibraryFileUpload::make('services')
                        ->label('Main image')
                        ->collection('services')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->imagePreviewHeight('150'),
                    TagsInput::make('keywords')->label('SEO keywords'),
                    TagsInput::make('highlights')->label('Highlights'),
                    TagsInput::make('industries')->label('Industries'),
                    TagsInput::make('brands')->label('Brands'),
                ])
                ->columns(2),

            Section::make('Translations and SEO (KA/EN/RU)')
                ->description('The main fields above stay as fallback content. These fields populate locale-specific frontend content automatically.')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'Service name'),
                    ...LocalizedContentFields::inputs('eyebrow', 'Eyebrow'),
                    ...LocalizedContentFields::inputs('title', 'Headline'),
                    ...LocalizedContentFields::inputs('description', 'Short description', textarea: true),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO title'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO description', textarea: true),
                    ...LocalizedContentFields::inputs('card.title', 'Card title'),
                    ...LocalizedContentFields::inputs('card.description', 'Card description', textarea: true),
                    LocalizedContentFields::customEntries('Examples: benefit.0.title, process.0.description, keyword.0, highlight.0'),
                ])
                ->columns(3),

            Section::make('Service blocks')
                ->schema([
                    Repeater::make('benefits')
                        ->label('Benefits')
                        ->schema(self::cardSchema())
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('solutions')
                        ->label('Solutions')
                        ->schema(self::cardSchema(true))
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('process')
                        ->label('Process')
                        ->schema([
                            TextInput::make('title')->label('Step')->required(),
                            Textarea::make('description')->label('Description')->required(),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Textarea::make('overview')
                        ->label('Overview JSON')
                        ->rule('json')
                        ->formatStateUsing(
                            fn ($state) => is_array($state)
                                ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                                : $state,
                        )
                        ->dehydrateStateUsing(
                            fn ($state) => is_string($state) ? json_decode($state, true) : $state,
                        )
                        ->helperText('Use structured JSON when you need custom overview blocks.'),
                    Textarea::make('warranty')->label('Warranty'),
                    Textarea::make('sla')->label('SLA terms'),
                ]),

            Section::make('Lead form and advanced calculator')
                ->description('These fields power both the frontend lead form and the pricing calculator.')
                ->schema([
                    Toggle::make('lead_form.calculator_enabled')
                        ->label('Enable calculator')
                        ->default(true),
                    Select::make('lead_form.pricing.currency')
                        ->label('Currency')
                        ->options([
                            'GEL' => 'GEL',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                        ])
                        ->default('GEL')
                        ->required(),
                    TextInput::make('lead_form.pricing.base_price')
                        ->label('Base one-time price')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.monthly_base_price')
                        ->label('Base monthly price')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.minimum_price')
                        ->label('Minimum one-time price')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.project_size_label_ka')
                        ->label('Project size label (KA)'),
                    TextInput::make('lead_form.project_size_label_en')
                        ->label('Project size label (EN)'),
                    TextInput::make('lead_form.project_size_label_ru')
                        ->label('Project size label (RU)'),
                    self::localizedOptionRepeater(
                        'lead_form.project_size_options',
                        'Project size options',
                    ),
                    TextInput::make('lead_form.property_type_label_ka')
                        ->label('Property type label (KA)'),
                    TextInput::make('lead_form.property_type_label_en')
                        ->label('Property type label (EN)'),
                    TextInput::make('lead_form.property_type_label_ru')
                        ->label('Property type label (RU)'),
                    self::localizedOptionRepeater(
                        'lead_form.property_type_options',
                        'Property type options',
                    ),
                    Repeater::make('lead_form.extra_fields')
                        ->label('Dynamic calculator fields')
                        ->schema([
                            TextInput::make('key')
                                ->label('Key')
                                ->required()
                                ->helperText('Example: router_count, camera_count'),
                            Select::make('type')
                                ->label('Field type')
                                ->options([
                                    'text' => 'Text',
                                    'number' => 'Number',
                                    'textarea' => 'Textarea',
                                    'select' => 'Select',
                                    'checkbox' => 'Checkbox',
                                ])
                                ->default('text')
                                ->required(),
                            Toggle::make('required')->label('Required'),
                            TextInput::make('ka')->label('Label (KA)')->required(),
                            TextInput::make('en')->label('Label (EN)'),
                            TextInput::make('ru')->label('Label (RU)'),
                            TextInput::make('placeholder_ka')->label('Placeholder (KA)'),
                            TextInput::make('placeholder_en')->label('Placeholder (EN)'),
                            TextInput::make('placeholder_ru')->label('Placeholder (RU)'),
                            TextInput::make('help_ka')->label('Help text (KA)'),
                            TextInput::make('help_en')->label('Help text (EN)'),
                            TextInput::make('help_ru')->label('Help text (RU)'),
                            TextInput::make('unit_ka')->label('Unit (KA)'),
                            TextInput::make('unit_en')->label('Unit (EN)'),
                            TextInput::make('unit_ru')->label('Unit (RU)'),
                            TextInput::make('min')->label('Min')->numeric(),
                            TextInput::make('max')->label('Max')->numeric(),
                            TextInput::make('step')->label('Step')->numeric(),
                            TextInput::make('default')->label('Default value'),
                            TextInput::make('unit_price')
                                ->label('One-time unit price')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_unit_price')
                                ->label('Monthly unit price')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('price_multiplier_field')
                                ->label('Price multiplier source key')
                                ->helperText('Use this when the selected option price should be multiplied by another numeric field.'),
                            Repeater::make('options')
                                ->label('Select options')
                                ->schema([
                                    TextInput::make('value')->label('Value')->required(),
                                    TextInput::make('ka')->label('Georgian')->required(),
                                    TextInput::make('en')->label('English'),
                                    TextInput::make('ru')->label('Russian'),
                                    TextInput::make('one_time_price')
                                        ->label('One-time price')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0),
                                    TextInput::make('monthly_price')
                                        ->label('Monthly price')
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
                        ->label('Calculator packages')
                        ->schema([
                            TextInput::make('key')
                                ->label('Key')
                                ->required()
                                ->helperText('Example: standard, business, managed'),
                            TextInput::make('title_ka')->label('Title (KA)')->required(),
                            TextInput::make('title_en')->label('Title (EN)'),
                            TextInput::make('title_ru')->label('Title (RU)'),
                            Textarea::make('description_ka')->label('Description (KA)')->rows(2),
                            Textarea::make('description_en')->label('Description (EN)')->rows(2),
                            Textarea::make('description_ru')->label('Description (RU)')->rows(2),
                            TextInput::make('one_time_price')
                                ->label('One-time price')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_price')
                                ->label('Monthly price')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            Toggle::make('recommended')->label('Recommended'),
                        ])
                        ->columns(3)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                    Textarea::make('lead_form.calculator_disclaimer_ka')
                        ->label('Disclaimer (KA)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_en')
                        ->label('Disclaimer (EN)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_ru')
                        ->label('Disclaimer (RU)')
                        ->rows(2),
                ])
                ->columns(3),

            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_published')->label('Published')->default(false),
                    TextInput::make('sort_order')->label('Sort order')->numeric()->default(0),
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
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('unique_viewers_count')
                    ->label('Unique views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_views_count')
                    ->label('Total views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('whatsapp_clicks_count')
                    ->label('WhatsApp clicks')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
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
