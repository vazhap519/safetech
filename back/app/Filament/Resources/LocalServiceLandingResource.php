<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocalServiceLandingResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Models\LocalServiceLanding;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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

class LocalServiceLandingResource extends Resource
{
    protected static ?string $model = LocalServiceLanding::class;

    protected static ?string $navigationLabel = 'Local SEO გვერდები';

    protected static ?string $modelLabel = 'Local SEO გვერდი';

    protected static ?string $pluralModelLabel = 'Local SEO გვერდები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('კომერციული Local SEO გვერდი')
                ->description('შექმენით მხოლოდ რეალური მომსახურების ზონისთვის უნიკალური, მომხმარებლისთვის სასარგებლო გვერდი. არ დააკოპიროთ ერთი და იგივე ტექსტი სხვადასხვა ქალაქზე მხოლოდ Google-ისთვის.')
                ->schema([
                    Select::make('service_id')
                        ->label('სერვისი')
                        ->relationship('service', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('location_name')
                        ->label('ქალაქი / მომსახურების ზონა (ქართული)')
                        ->required()
                        ->maxLength(255),
                    ...LocalizedContentFields::secondaryInputs('locationName', 'ქალაქი / მომსახურების ზონა'),
                    TextInput::make('location_slug')
                        ->label('URL slug')
                        ->required()
                        ->maxLength(120)
                        ->helperText('მაგალითი: tbilisi. საბოლოო URL: /services/{service}/{location}.'),
                    TextInput::make('eyebrow')
                        ->label('მოკლე ზედა ტექსტი (ქართული)')
                        ->maxLength(255),
                    ...LocalizedContentFields::secondaryInputs('eyebrow', 'მოკლე ზედა ტექსტი'),
                    TextInput::make('title')
                        ->label('H1 სათაური (ქართული)')
                        ->required()
                        ->maxLength(255),
                    ...LocalizedContentFields::secondaryInputs('title', 'H1 სათაური', maxLength: 255),
                    Textarea::make('excerpt')
                        ->label('მოკლე გაყიდვითი აღწერა (ქართული)')
                        ->rows(4),
                    ...LocalizedContentFields::secondaryInputs('excerpt', 'მოკლე გაყიდვითი აღწერა', textarea: true, rows: 4),
                    Textarea::make('content')
                        ->label('ძირითადი უნიკალური კონტენტი (ქართული)')
                        ->required()
                        ->rows(16)
                        ->helperText('აღწერეთ ადგილობრივი სამუშაო პროცესი, ობიექტების ტიპები, ტექნიკური მიდგომა და რეალური გამოცდილება. ტექსტი უნდა იყოს უნიკალური ამ გვერდისთვის.'),
                    ...LocalizedContentFields::secondaryInputs('content', 'ძირითადი უნიკალური კონტენტი', textarea: true, rows: 16),
                ])
                ->columns(3),

            Section::make('სარგებელი და FAQ')
                ->schema([
                    Repeater::make('benefits')
                        ->label('რატომ SafeTech / რას მიიღებს კლიენტი')
                        ->schema([
                            TextInput::make('title')->label('სათაური (ქართული)')->required(),
                            ...LocalizedContentFields::itemInputs('title', 'სათაური'),
                            Textarea::make('description')->label('აღწერა (ქართული)')->required()->rows(3),
                            ...LocalizedContentFields::itemInputs('description', 'აღწერა', textarea: true, rows: 3),
                        ])
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('faq')
                        ->label('ადგილობრივი FAQ')
                        ->schema([
                            TextInput::make('question')->label('კითხვა (ქართული)')->required(),
                            ...LocalizedContentFields::itemInputs('question', 'კითხვა'),
                            Textarea::make('answer')->label('პასუხი (ქართული)')->required()->rows(4),
                            ...LocalizedContentFields::itemInputs('answer', 'პასუხი', textarea: true, rows: 4),
                        ])
                        ->columns(3)
                        ->collapsible(),
                ]),

            Section::make('რეალური პროექტები — Topic Cluster')
                ->description('აირჩიეთ შესაბამისი განხორციელებული პროექტები. ისინი გამოჩნდება გვერდზე როგორც რეალური მტკიცებულება და შექმნის სერვისი → ქალაქი → პროექტი შიდა ბმულების კლასტერს.')
                ->schema([
                    Select::make('projects')
                        ->label('დაკავშირებული რეალური პროექტები')
                        ->relationship('projects', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('კონვერსია')
                ->schema([
                    TextInput::make('cta_title')->label('CTA სათაური (ქართული)')->maxLength(255),
                    ...LocalizedContentFields::secondaryInputs('ctaTitle', 'CTA სათაური', maxLength: 255),
                    Textarea::make('cta_text')->label('CTA ტექსტი (ქართული)')->rows(3),
                    ...LocalizedContentFields::secondaryInputs('ctaText', 'CTA ტექსტი', textarea: true, rows: 3),
                ])
                ->columns(3),

            Section::make('SEO')
                ->schema([
                    TextInput::make('primary_keyword')
                        ->label('ძირითადი კომერციული keyword (ქართული)')
                        ->helperText('მაგ: უსაფრთხოების კამერების მონტაჟი თბილისში'),
                    ...LocalizedContentFields::secondaryInputs('primaryKeyword', 'ძირითადი კომერციული keyword'),
                    TagsInput::make('keywords')->label('დამხმარე keywords (ქართული)'),
                    TagsInput::make('translations.keywords.en')->label('დამხმარე keywords (ინგლისური)'),
                    TagsInput::make('translations.keywords.ru')->label('დამხმარე keywords (რუსული)'),
                    TextInput::make('seo_title')->label('SEO title (ქართული)')->maxLength(255),
                    ...LocalizedContentFields::secondaryInputs('seoTitle', 'SEO title', maxLength: 255),
                    Textarea::make('seo_description')->label('SEO description (ქართული)')->rows(3)->maxLength(320),
                    ...LocalizedContentFields::secondaryInputs('seoDescription', 'SEO description', textarea: true, maxLength: 320),
                    Toggle::make('noindex')
                        ->label('Noindex')
                        ->default(true)
                        ->helperText('ჩართეთ, სანამ გვერდი სრულად მზად არ არის ან უნიკალური კონტენტი არ აქვს.'),
                ])
                ->columns(3),

            Section::make('გამოქვეყნება')
                ->schema([
                    Toggle::make('is_published')->label('Published')->default(false),
                    DateTimePicker::make('published_at')->label('Published at'),
                    TextInput::make('sort_order')->label('Sort order')->numeric()->default(0),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.name')->label('სერვისი')->searchable()->sortable(),
                TextColumn::make('location_name')->label('ლოკაცია')->searchable(),
                TextColumn::make('title')->label('სათაური')->searchable()->limit(60),
                TextColumn::make('primary_keyword')->label('Primary keyword')->limit(50),
                IconColumn::make('is_published')->label('Published')->boolean(),
                IconColumn::make('noindex')->label('Noindex')->boolean(),
                TextColumn::make('updated_at')->label('განახლდა')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocalServiceLandings::route('/'),
            'create' => Pages\CreateLocalServiceLanding::route('/create'),
            'edit' => Pages\EditLocalServiceLanding::route('/{record}/edit'),
        ];
    }
}
