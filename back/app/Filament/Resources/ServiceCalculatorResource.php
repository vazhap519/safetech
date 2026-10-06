<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCalculatorResource\Pages;
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

class ServiceCalculatorResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationLabel = 'კალკულატორი და კონფიგურატორები';

    protected static ?string $modelLabel = 'სერვისის კალკულატორი';

    protected static ?string $pluralModelLabel = 'კალკულატორი და კონფიგურატორები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 12;

    private static function localizedOptionRepeater(string $name, string $label): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                TextInput::make('value')->label('Value')->required()->helperText('Example: small, office, hotel'),
                TextInput::make('ka')->label('Georgian')->required(),
                TextInput::make('en')->label('English'),
                TextInput::make('ru')->label('Russian'),
                TextInput::make('one_time_price')->label('One-time price delta')->numeric()->minValue(0)->default(0)->suffix('GEL'),
                TextInput::make('monthly_price')->label('Monthly price delta')->numeric()->minValue(0)->default(0)->suffix('GEL'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Service')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                IconColumn::make('lead_form.calculator_enabled')->label('Calculator')->boolean(),
                TextColumn::make('lead_form.pricing.currency')->label('Currency')->default('GEL'),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()->label('კონფიგურაცია')]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceCalculators::route('/'),
            'edit' => Pages\EditServiceCalculator::route('/{record}/edit'),
        ];
    }
}
