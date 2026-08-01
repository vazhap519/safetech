<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceConfiguratorResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\Service;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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

class ServiceConfiguratorResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationLabel = 'კალკულატორი და კონფიგურატორი';

    protected static ?string $modelLabel = 'სერვისის კონფიგურატორი';

    protected static ?string $pluralModelLabel = 'სერვისების კონფიგურატორები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /** @return array<int, mixed> */
    private static function localizedFields(string $prefix, string $label): array
    {
        return [
            TextInput::make("{$prefix}_ka")->label("{$label} (KA)"),
            TextInput::make("{$prefix}_en")->label("{$label} (EN)"),
            TextInput::make("{$prefix}_ru")->label("{$label} (RU)"),
        ];
    }

    private static function optionRepeater(string $name, string $label): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                TextInput::make('value')
                    ->label('ტექნიკური მნიშვნელობა')
                    ->required()
                    ->helperText('მაგალითი: ip, 4mp, small'),
                TextInput::make('ka')->label('დასახელება (KA)')->required(),
                TextInput::make('en')->label('დასახელება (EN)'),
                TextInput::make('ru')->label('დასახელება (RU)'),
                TextInput::make('one_time_price')
                    ->label('ერთჯერადი ფასის ცვლილება')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('monthly_price')
                    ->label('ყოველთვიური ფასის ცვლილება')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ])
            ->columns(3)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('არჩეული სერვისი')
                ->description('კალკულატორის კონფიგურაცია ინახება უშუალოდ ამ სერვისში.')
                ->schema([
                    TextInput::make('name')
                        ->label('სერვისი')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated(false),
                    Toggle::make('lead_form.calculator_enabled')
                        ->label('კალკულატორი ჩართულია')
                        ->default(true),
                ])
                ->columns(3),

            Section::make('ფასები, მომსახურება და ფასდაკლება')
                ->description('ყველა თანხა იცვლება ადმინ პანელიდან. მომხმარებელი ფასდაკლების პროცენტს თვითონ ვერ ცვლის.')
                ->schema([
                    Select::make('lead_form.pricing.currency')
                        ->label('ვალუტა')
                        ->options([
                            'GEL' => 'GEL',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                        ])
                        ->default('GEL')
                        ->required(),
                    TextInput::make('lead_form.pricing.base_price')
                        ->label('სერვისის საბაზო საფასური')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.monthly_base_price')
                        ->label('ყოველთვიური საბაზო საფასური')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.minimum_price')
                        ->label('მინიმალური პროექტის ღირებულება')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.labor_price')
                        ->label('ფიქსირებული სამუშაოს საფასური')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                    TextInput::make('lead_form.pricing.discount_percentage')
                        ->label('ფასდაკლება (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->default(0),
                ])
                ->columns(3),

            Section::make('პროექტის ტიპები და მასშტაბები')
                ->description('ეს არჩევანი გამოჩნდება კალკულატორის ზედა ნაწილში.')
                ->schema([
                    ...self::localizedFields('lead_form.project_size_label', 'მასშტაბის ველი'),
                    self::optionRepeater(
                        'lead_form.project_size_options',
                        'მასშტაბის ვარიანტები',
                    ),
                    ...self::localizedFields('lead_form.property_type_label', 'ობიექტის ტიპის ველი'),
                    self::optionRepeater(
                        'lead_form.property_type_options',
                        'ობიექტის ტიპები',
                    ),
                ])
                ->columns(3),

            Section::make('მომსახურების პარამეტრები')
                ->description('მაგალითად: კამერების რაოდენობა, ტექნოლოგია, გარჩევადობა, ობიექტივი, კაბელის მეტრაჟი.')
                ->schema([
                    Repeater::make('lead_form.extra_fields')
                        ->label('კალკულატორის ველები')
                        ->schema([
                            TextInput::make('key')
                                ->label('ველის გასაღები')
                                ->required()
                                ->helperText('მაგალითი: camera_count, resolution, lens'),
                            Select::make('type')
                                ->label('ველის ტიპი')
                                ->options([
                                    'text' => 'ტექსტი',
                                    'number' => 'რიცხვი',
                                    'textarea' => 'დიდი ტექსტი',
                                    'select' => 'არჩევანი',
                                    'checkbox' => 'ჩასართავი',
                                ])
                                ->default('text')
                                ->required()
                                ->live(),
                            Toggle::make('required')->label('სავალდებულო'),
                            TextInput::make('ka')->label('დასახელება (KA)')->required(),
                            TextInput::make('en')->label('დასახელება (EN)'),
                            TextInput::make('ru')->label('დასახელება (RU)'),
                            TextInput::make('placeholder_ka')->label('Placeholder (KA)'),
                            TextInput::make('placeholder_en')->label('Placeholder (EN)'),
                            TextInput::make('placeholder_ru')->label('Placeholder (RU)'),
                            TextInput::make('help_ka')->label('დახმარება (KA)'),
                            TextInput::make('help_en')->label('დახმარება (EN)'),
                            TextInput::make('help_ru')->label('დახმარება (RU)'),
                            TextInput::make('unit_ka')->label('ერთეული (KA)'),
                            TextInput::make('unit_en')->label('ერთეული (EN)'),
                            TextInput::make('unit_ru')->label('ერთეული (RU)'),
                            TextInput::make('min')->label('მინიმუმი')->numeric(),
                            TextInput::make('max')->label('მაქსიმუმი')->numeric(),
                            TextInput::make('step')->label('ნაბიჯი')->numeric(),
                            TextInput::make('default')->label('საწყისი მნიშვნელობა'),
                            TextInput::make('unit_price')
                                ->label('მომსახურების ერთეულის ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_unit_price')
                                ->label('ყოველთვიური ერთეულის ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('price_multiplier_field')
                                ->label('ფასის გამრავლების ველი')
                                ->helperText('მაგალითად არჩევანის ფასი გაამრავლე camera_count-ზე.'),
                            Repeater::make('options')
                                ->label('არჩევანის ვარიანტები')
                                ->schema([
                                    TextInput::make('value')->label('მნიშვნელობა')->required(),
                                    TextInput::make('ka')->label('KA')->required(),
                                    TextInput::make('en')->label('EN'),
                                    TextInput::make('ru')->label('RU'),
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
                                ->columns(3)
                                ->default([])
                                ->collapsible()
                                ->reorderable()
                                ->visible(
                                    fn (Get $get): bool => ($get('type') ?? 'text') === 'select',
                                ),
                        ])
                        ->columns(3)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                ]),

            Section::make('მომსახურების პაკეტები')
                ->schema([
                    Repeater::make('lead_form.packages')
                        ->label('პაკეტები')
                        ->schema([
                            TextInput::make('key')->label('გასაღები')->required(),
                            TextInput::make('title_ka')->label('სათაური (KA)')->required(),
                            TextInput::make('title_en')->label('სათაური (EN)'),
                            TextInput::make('title_ru')->label('სათაური (RU)'),
                            Textarea::make('description_ka')->label('აღწერა (KA)')->rows(2),
                            Textarea::make('description_en')->label('აღწერა (EN)')->rows(2),
                            Textarea::make('description_ru')->label('აღწერა (RU)')->rows(2),
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
                ]),

            Section::make('კომპონენტების კატალოგი და თავსებადობა')
                ->description('აქ ემატება მოწყობილობები, მასალები და სამუშაოები. წესები განსაზღვრავს, როდის უნდა შესთავაზოს სისტემა კონკრეტული NVR, DVR, სვიჩი, დისკი ან სხვა კომპონენტი.')
                ->schema([
                    Repeater::make('lead_form.components')
                        ->label('კომპონენტები')
                        ->schema([
                            TextInput::make('key')
                                ->label('უნიკალური გასაღები')
                                ->required()
                                ->helperText('მაგალითი: nvr-16ch-poe'),
                            Select::make('category')
                                ->label('კატეგორია')
                                ->options([
                                    'camera' => 'კამერა',
                                    'recorder' => 'ჩამწერი NVR/DVR',
                                    'storage' => 'დისკი/საცავი',
                                    'network' => 'ქსელი/PoE',
                                    'cabling' => 'კაბელი',
                                    'power' => 'კვება/UPS',
                                    'intercom' => 'ინტერკომი',
                                    'lock' => 'საკეტი',
                                    'accessory' => 'აქსესუარი',
                                    'server' => 'სერვერი',
                                    'labor' => 'სამუშაო',
                                    'other' => 'სხვა',
                                ])
                                ->default('other')
                                ->required(),
                            TextInput::make('exclusive_group')
                                ->label('ექსკლუზიური ჯგუფი')
                                ->helperText('ერთ ჯგუფში მხოლოდ ყველაზე მაღალი პრიორიტეტის თავსებადი კომპონენტი აირჩევა. მაგალითი: recorder.'),
                            TextInput::make('priority')
                                ->label('პრიორიტეტი')
                                ->numeric()
                                ->default(0),
                            TextInput::make('title_ka')->label('დასახელება (KA)')->required(),
                            TextInput::make('title_en')->label('დასახელება (EN)'),
                            TextInput::make('title_ru')->label('დასახელება (RU)'),
                            Textarea::make('description_ka')->label('აღწერა (KA)')->rows(2),
                            Textarea::make('description_en')->label('აღწერა (EN)')->rows(2),
                            Textarea::make('description_ru')->label('აღწერა (RU)')->rows(2),
                            TextInput::make('unit_price')
                                ->label('ერთეულის ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('monthly_price')
                                ->label('ყოველთვიური ერთეულის ფასი')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            Select::make('quantity_mode')
                                ->label('რაოდენობის გამოთვლა')
                                ->options([
                                    'fixed' => 'ფიქსირებული რაოდენობა',
                                    'field' => 'პირდაპირ ველიდან',
                                    'ceil' => 'ველის გაყოფა ტევადობაზე და დამრგვალება ზემოთ',
                                ])
                                ->default('fixed')
                                ->required()
                                ->live(),
                            TextInput::make('quantity_field')
                                ->label('რაოდენობის წყარო ველი')
                                ->helperText('მაგალითი: camera_count')
                                ->visible(
                                    fn (Get $get): bool => in_array(
                                        $get('quantity_mode'),
                                        ['field', 'ceil'],
                                        true,
                                    ),
                                ),
                            TextInput::make('default_quantity')
                                ->label('ფიქსირებული რაოდენობა')
                                ->numeric()
                                ->minValue(0)
                                ->default(1),
                            TextInput::make('units_per_component')
                                ->label('ტევადობა ერთ კომპონენტზე')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->helperText('მაგალითად 16-არხიანი NVR-ისთვის მიუთითე 16.')
                                ->visible(
                                    fn (Get $get): bool => $get('quantity_mode') === 'ceil',
                                ),
                            TextInput::make('minimum_quantity')
                                ->label('მინ. რაოდენობა')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                            TextInput::make('maximum_quantity')
                                ->label('მაქს. რაოდენობა')
                                ->numeric()
                                ->minValue(1),
                            Toggle::make('required')
                                ->label('აუცილებელია — მომხმარებელი ვერ გამორთავს'),
                            Toggle::make('recommended')
                                ->label('რეკომენდებულია — ავტომატურად მონიშნული'),
                            Repeater::make('rules')
                                ->label('თავსებადობის წესები')
                                ->schema([
                                    TextInput::make('field')
                                        ->label('ველის გასაღები')
                                        ->required()
                                        ->helperText('camera_count, resolution, lens, project_size, property_type, package'),
                                    Select::make('operator')
                                        ->label('ოპერატორი')
                                        ->options([
                                            'equals' => 'უდრის',
                                            'not_equals' => 'არ უდრის',
                                            'gte' => 'მეტია ან ტოლია',
                                            'lte' => 'ნაკლებია ან ტოლია',
                                            'contains' => 'შეიცავს',
                                            'truthy' => 'ჩართულია',
                                            'falsy' => 'გამორთულია',
                                        ])
                                        ->default('equals')
                                        ->required(),
                                    TextInput::make('value')
                                        ->label('მნიშვნელობა')
                                        ->helperText('truthy/falsy ოპერატორზე შეიძლება ცარიელი დარჩეს.'),
                                ])
                                ->columns(3)
                                ->default([])
                                ->collapsible()
                                ->reorderable(),
                        ])
                        ->columns(4)
                        ->default([])
                        ->collapsible()
                        ->reorderable()
                        ->itemLabel(
                            fn (array $state): ?string => $state['title_ka'] ?? $state['key'] ?? null,
                        ),
                ]),

            Section::make('განმარტება')
                ->schema([
                    Textarea::make('lead_form.calculator_disclaimer_ka')
                        ->label('განმარტება (KA)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_en')
                        ->label('განმარტება (EN)')
                        ->rows(2),
                    Textarea::make('lead_form.calculator_disclaimer_ru')
                        ->label('განმარტება (RU)')
                        ->rows(2),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('სერვისი')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                IconColumn::make('lead_form.calculator_enabled')
                    ->label('კალკულატორი')
                    ->boolean(),
                TextColumn::make('lead_form.pricing.base_price')
                    ->label('სერვისის ფასი')
                    ->money('GEL'),
                TextColumn::make('lead_form.pricing.discount_percentage')
                    ->label('ფასდაკლება')
                    ->suffix('%'),
                TextColumn::make('updated_at')
                    ->label('განახლებული')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()->label('კონფიგურაცია')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceConfigurators::route('/'),
            'edit' => Pages\EditServiceConfigurator::route('/{record}/edit'),
        ];
    }
}
