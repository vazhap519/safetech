<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\AdminIconOptions;
use App\Filament\Support\GeneratedSchemaPreview;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Filament\Support\StableSlug;
use App\Filament\Support\StructuredDataJsonField;
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
            ...LocalizedContentFields::itemInputs('title', 'Title'),
            Textarea::make('description')->label('Description')->required(),
            ...LocalizedContentFields::itemInputs('description', 'Description', textarea: true),
            ...($featured ? [Toggle::make('featured')->label('Featured')] : []),
        ];
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
                        ->live()
                        ->afterStateUpdated(StableSlug::syncOnCreate()),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly()
                        ->helperText('Generated automatically from the Georgian name. Edit the name to update it.'),
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
                    TagsInput::make('translations.keywords.en')->label('SEO keywords (EN)'),
                    TagsInput::make('translations.keywords.ru')->label('SEO keywords (RU)'),
                    TagsInput::make('highlights')->label('Highlights'),
                    TagsInput::make('translations.highlights.en')->label('Highlights (EN)'),
                    TagsInput::make('translations.highlights.ru')->label('Highlights (RU)'),
                    TagsInput::make('industries')->label('Industries'),
                    TagsInput::make('translations.industries.en')->label('Industries (EN)'),
                    TagsInput::make('translations.industries.ru')->label('Industries (RU)'),
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
                    ...LocalizedContentFields::inputs('ogTitle', 'Open Graph title'),
                    ...LocalizedContentFields::inputs('ogDescription', 'Open Graph description', textarea: true),
                    ...LocalizedContentFields::inputs('card.title', 'Card title'),
                    ...LocalizedContentFields::inputs('card.description', 'Card description', textarea: true),
                    LocalizedContentFields::customEntries('Examples: benefit.0.title, process.0.description, keyword.0, highlight.0'),
                ])
                ->columns(3),

            Section::make('Technical SEO')
                ->description('Canonical, robots, Schema.org and social preview settings for this service page.')
                ->schema([
                    TextInput::make('seo.canonical')->label('Canonical URL override')->url(),
                    Select::make('seo.schema_type')->label('Schema.org type')->options([
                        'Service' => 'Service',
                        'Product' => 'Product',
                        'WebPage' => 'WebPage',
                    ])->default('Service'),
                    SpatieMediaLibraryFileUpload::make('og_image')
                        ->label('Open Graph image')
                        ->helperText('რეკომენდებული ზომაა 1200×630. ატვირთვისას ავტომატურად იქმნება WebP ვერსია.')
                        ->collection('og_image')
                        ->conversion('og')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->imagePreviewHeight('150'),
                    Toggle::make('seo.noindex')->label('Noindex')->default(false),
                ])->columns(2),

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
                            ...LocalizedContentFields::itemInputs('title', 'Step'),
                            Textarea::make('description')->label('Description')->required(),
                            ...LocalizedContentFields::itemInputs('description', 'Description', textarea: true),
                        ])
                        ->columns(3)
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
                    Textarea::make('warranty')->label('Warranty (KA)'),
                    ...LocalizedContentFields::secondaryInputs('warranty', 'Warranty', textarea: true),
                    Textarea::make('sla')->label('SLA terms (KA)'),
                    ...LocalizedContentFields::secondaryInputs('sla', 'SLA terms', textarea: true),
                ]),

            Section::make('Schema JSON-LD')
                ->description('სერვისის structured data ავტომატურად გენერირდება. Custom override გამოიყენეთ მხოლოდ მაშინ, როცა ავტომატური schema მთლიანად უნდა ჩაანაცვლოთ ან გააფართოოთ.')
                ->schema([
                    StructuredDataJsonField::makeAt(
                        'seo.schema',
                        'ცარიელი დატოვეთ ავტომატური Schema-სთვის. აქ შეყვანილი JSON frontend-ზე ემატება სერვისის structured data-ს.',
                    ),
                    GeneratedSchemaPreview::service(),
                ]),

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
