<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
                TextInput::make('en')->label('English'),
                TextInput::make('ru')->label('Русский'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    /** @return array<int, TextInput|Textarea> */
    private static function translationInputs(string $field, string $label, bool $textarea = false): array
    {
        return collect([
            'ka' => 'ქართული',
            'en' => 'English',
            'ru' => 'Русский',
        ])->map(
            fn (string $localeLabel, string $locale) => ($textarea
                ? Textarea::make("translations.fields.{$field}.{$locale}")->rows(3)
                : TextInput::make("translations.fields.{$field}.{$locale}")
            )->label("{$label} ({$localeLabel})"),
        )->values()->all();
    }

    private static function customTranslationEntries(): Repeater
    {
        return Repeater::make('translations.entries')
            ->label('დამატებითი თარგმნის key-ები')
            ->schema([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->helperText('მაგ: benefit.0.title, process.0.description, keyword.0, highlight.0'),
                TextInput::make('ka')->label('ქართული'),
                TextInput::make('en')->label('English'),
                TextInput::make('ru')->label('Русский'),
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
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('მაგ: networking'),
                    TextInput::make('eyebrow')->label('ზედა კატეგორია'),
                    TextInput::make('icon')->label('Material icon')->default('settings')->required(),
                    TextInput::make('title')->label('მთავარი სათაური')->required(),
                    Textarea::make('description')->label('მოკლე აღწერა')->required()->rows(3),
                    Textarea::make('seo_description')
                        ->label('SEO აღწერა')
                        ->required()
                        ->rows(3)
                        ->maxLength(320),
                    FileUpload::make('hero_image')
                        ->label('მთავარი ფოტო')
                        ->image()
                        ->disk('public')
                        ->directory('services'),
                    TagsInput::make('keywords')->label('SEO საკვანძო სიტყვები'),
                    TagsInput::make('highlights')->label('მთავარი უპირატესობები'),
                    TagsInput::make('industries')->label('ინდუსტრიები'),
                    TagsInput::make('brands')->label('ბრენდები'),
                ])
                ->columns(2),

            Section::make('კონტენტი და SEO 3 ენაზე')
                ->description('ზედა ძირითადი ველები რჩება ქართულ fallback-ად. აქედან KA/EN/RU ტექსტები ფრონტზე ავტომატურად წავა service.{slug} key-ებით.')
                ->schema([
                    ...self::translationInputs('name', 'სერვისის სახელი'),
                    ...self::translationInputs('eyebrow', 'ზედა კატეგორია'),
                    ...self::translationInputs('title', 'მთავარი სათაური'),
                    ...self::translationInputs('description', 'მოკლე აღწერა', true),
                    ...self::translationInputs('seoDescription', 'SEO აღწერა', true),
                    ...self::translationInputs('card.title', 'ბარათის სათაური'),
                    ...self::translationInputs('card.description', 'ბარათის აღწერა', true),
                    self::customTranslationEntries(),
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
                        ->helperText('სტრუქტურირებული overview ობიექტი JSON ფორმატში.'),
                    Textarea::make('warranty')->label('გარანტია'),
                    Textarea::make('sla')->label('SLA პირობები'),
                ]),

            Section::make('კალკულატორის დინამიური ველები')
                ->description('სერვისის არჩევისას საკონტაქტო ფორმაზე გამოჩნდება ზუსტად ეს ველები.')
                ->schema([
                    TextInput::make('lead_form.project_size_label_ka')
                        ->label('პროექტის ზომის სათაური (KA)'),
                    TextInput::make('lead_form.project_size_label_en')
                        ->label('Project size label (EN)'),
                    TextInput::make('lead_form.project_size_label_ru')
                        ->label('Название размера проекта (RU)'),
                    self::localizedOptionRepeater(
                        'lead_form.project_size_options',
                        'პროექტის ზომის ვარიანტები',
                    ),
                    TextInput::make('lead_form.property_type_label_ka')
                        ->label('ობიექტის ტიპის სათაური (KA)'),
                    TextInput::make('lead_form.property_type_label_en')
                        ->label('Property type label (EN)'),
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
                                ])
                                ->default('text')
                                ->required(),
                            Toggle::make('required')->label('სავალდებულო'),
                            TextInput::make('ka')->label('სათაური (KA)')->required(),
                            TextInput::make('en')->label('Label (EN)'),
                            TextInput::make('ru')->label('Название (RU)'),
                            TextInput::make('placeholder_ka')->label('Placeholder (KA)'),
                            TextInput::make('placeholder_en')->label('Placeholder (EN)'),
                            TextInput::make('placeholder_ru')->label('Placeholder (RU)'),
                            Repeater::make('options')
                                ->label('არჩევანის ვარიანტები')
                                ->schema([
                                    TextInput::make('value')
                                        ->label('კოდი / მნიშვნელობა')
                                        ->required(),
                                    TextInput::make('ka')->label('ქართული')->required(),
                                    TextInput::make('en')->label('English'),
                                    TextInput::make('ru')->label('Русский'),
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
                TextColumn::make('slug')->searchable(),
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
