<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\Estimate;
use App\Support\Estimators\EstimateCalculator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationLabel = 'კალკულატორი';

    protected static ?string $modelLabel = 'შეფასება';

    protected static ?string $pluralModelLabel = 'შეფასებები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Sales;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('პროექტი და კლიენტი')
                ->schema([
                    TextInput::make('estimate_number')
                        ->label('შეთავაზების კოდი')
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Select::make('project_type')
                        ->label('პროექტის ტიპი')
                        ->options([
                            'cctv' => 'CCTV',
                            'network' => 'Network',
                            'it' => 'IT',
                        ])
                        ->default('cctv')
                        ->required()
                        ->live(),
                    TextInput::make('client_name')
                        ->label('კლიენტის სახელი'),
                    TextInput::make('company')
                        ->label('კომპანია'),
                    TextInput::make('email')
                        ->label('ელფოსტა')
                        ->email(),
                    TextInput::make('phone')
                        ->label('ტელეფონი'),
                ])
                ->columns(2),

            Section::make('CCTV პარამეტრები')
                ->description('საცავის კალკულატორი ავტომატურად ითვლის HDD/NVR/PoE საჭიროებებს.')
                ->visible(fn (Get $get): bool => self::isCctv($get))
                ->schema([
                    Select::make('camera_type')
                        ->label('კამერის ტიპი')
                        ->options([
                            'ip' => 'IP',
                            'analog' => 'Analog',
                        ])
                        ->default('ip')
                        ->required()
                        ->live(),
                    TextInput::make('camera_count')
                        ->label('კამერების რაოდენობა')
                        ->numeric()
                        ->default(4)
                        ->minValue(0)
                        ->required()
                        ->live(onBlur: true),
                    TextInput::make('camera_megapixels')
                        ->label('მეგაპიქსელი')
                        ->numeric()
                        ->default(4)
                        ->minValue(1)
                        ->step(0.1)
                        ->required()
                        ->live(onBlur: true),
                    Select::make('frames_per_second')
                        ->label('FPS')
                        ->options([
                            12 => '12 FPS',
                            15 => '15 FPS',
                            20 => '20 FPS',
                            25 => '25 FPS',
                            30 => '30 FPS',
                        ])
                        ->default(15)
                        ->required()
                        ->live(),
                    Select::make('recording_hours_per_day')
                        ->label('ჩაწერა დღეში')
                        ->options([
                            8 => '8 საათი',
                            12 => '12 საათი',
                            16 => '16 საათი',
                            24 => '24 საათი',
                        ])
                        ->default(24)
                        ->required()
                        ->live(),
                    Select::make('recording_days')
                        ->label('HDD დღეები')
                        ->options([
                            7 => '7 დღე',
                            15 => '15 დღე',
                            30 => '30 დღე',
                            60 => '60 დღე',
                        ])
                        ->default(30)
                        ->required()
                        ->live(),
                ])
                ->columns(3),

            Section::make('ღირებულებები')
                ->schema([
                    TextInput::make('camera_unit_cost')
                        ->label('კამერის ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => self::isCctv($get)),
                    TextInput::make('recorder_unit_cost')
                        ->label('NVR / DVR ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => self::isCctv($get)),
                    TextInput::make('hdd_cost_per_tb')
                        ->label('HDD ფასი / 1TB')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => self::isCctv($get)),
                    TextInput::make('poe_switch_unit_cost')
                        ->label('PoE სვიჩის ფასი / 8 პორტი')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => self::isCctv($get) && self::isIpCamera($get)),
                    TextInput::make('connector_unit_cost')
                        ->label('RJ45 / BNC ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->visible(fn (Get $get): bool => self::isCctv($get)),
                    TextInput::make('cable_meters')
                        ->label('კაბელი (მეტრი)')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('cable_unit_cost')
                        ->label('კაბელის ფასი / მ')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('trunking_meters')
                        ->label('კაბელარხი (მეტრი)')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('trunking_unit_cost')
                        ->label('კაბელარხის ფასი / მ')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('installation_cost')
                        ->label('მონტაჟის ფასი')
                        ->prefix('₾')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true),
                    TextInput::make('markup_rate')
                        ->label('ფასნამატი')
                        ->helperText('0.60 = 60%')
                        ->numeric()
                        ->default(0.6)
                        ->step(0.01)
                        ->minValue(0)
                        ->required()
                        ->live(onBlur: true),
                ])
                ->columns(3),

            Section::make('დამატებითი კომპონენტები')
                ->description('ქსელისა და IT პროექტებისთვის აქ შეგიძლიათ ხელით დაამატოთ როუტერები, სვიჩები, კარადები, UPS და სხვა ხაზები.')
                ->schema([
                    Repeater::make('manual_items')
                        ->label('დამატებითი ხაზები')
                        ->schema([
                            TextInput::make('label')
                                ->label('კომპონენტი')
                                ->required(),
                            TextInput::make('quantity')
                                ->label('რაოდენობა')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            Select::make('unit')
                                ->label('ერთეული')
                                ->options([
                                    'pcs' => 'pcs',
                                    'm' => 'm',
                                    'job' => 'job',
                                    'set' => 'set',
                                    'tb' => 'tb',
                                ])
                                ->default('pcs')
                                ->required(),
                            TextInput::make('unit_cost')
                                ->label('თვითღირებულება / ერთეული')
                                ->prefix('₾')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])
                        ->columns(4)
                        ->default([])
                        ->columnSpanFull()
                        ->collapsible()
                        ->reorderable(),
                ]),

            Section::make('ცოცხალი შეჯამება')
                ->schema([
                    Placeholder::make('requirements_preview')
                        ->label('საჭირო აღჭურვილობა')
                        ->content(fn (Get $get): array => self::requirementsPreview($get))
                        ->listWithLineBreaks(),
                    Placeholder::make('pricing_preview')
                        ->label('ფინანსური შეჯამება')
                        ->content(fn (Get $get): array => self::pricingPreview($get))
                        ->listWithLineBreaks(),
                    Placeholder::make('storage_preview')
                        ->label('საცავის გამოთვლა')
                        ->content(fn (Get $get): array => self::storagePreview($get))
                        ->listWithLineBreaks()
                        ->visible(fn (Get $get): bool => self::isCctv($get)),
                    Placeholder::make('pdf_preview')
                        ->label('PDF ხაზების წინასწარი ნახვა')
                        ->content(fn (Get $get): array => self::pdfLinePreview($get))
                        ->listWithLineBreaks()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('შიდა შენიშვნები')
                ->schema([
                    Textarea::make('notes')
                        ->label('შიდა შენიშვნები')
                        ->rows(5),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('estimate_number')
                    ->label('კოდი')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client_name')
                    ->label('კლიენტი')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('company')
                    ->label('კომპანია')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('project_type')
                    ->label('ტიპი')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),
                TextColumn::make('final_total')
                    ->label('საბოლოო ფასი')
                    ->formatStateUsing(fn ($state): string => self::formatMoney((float) $state))
                    ->sortable(),
                TextColumn::make('profit_total')
                    ->label('მოგება')
                    ->formatStateUsing(fn ($state): string => self::formatMoney((float) $state))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('ავტორი')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('შექმნილია')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('project_type')
                    ->label('ტიპი')
                    ->options([
                        'cctv' => 'CCTV',
                        'network' => 'Network',
                        'it' => 'IT',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Estimate $record): string => route('admin.estimates.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function hydrateCalculatedFields(array $data): array
    {
        return array_merge($data, app(EstimateCalculator::class)->calculate($data));
    }

    private static function isCctv(Get $get): bool
    {
        return ($get('project_type') ?? 'cctv') === 'cctv';
    }

    private static function isIpCamera(Get $get): bool
    {
        return ($get('camera_type') ?? 'ip') === 'ip';
    }

    /**
     * @return array<string, mixed>
     */
    private static function previewCalculation(Get $get): array
    {
        return app(EstimateCalculator::class)->calculate([
            'project_type' => $get('project_type') ?? 'cctv',
            'camera_type' => $get('camera_type') ?? 'ip',
            'camera_count' => $get('camera_count') ?? 0,
            'camera_megapixels' => $get('camera_megapixels') ?? 4,
            'frames_per_second' => $get('frames_per_second') ?? 15,
            'recording_hours_per_day' => $get('recording_hours_per_day') ?? 24,
            'recording_days' => $get('recording_days') ?? 30,
            'camera_unit_cost' => $get('camera_unit_cost') ?? 0,
            'recorder_unit_cost' => $get('recorder_unit_cost') ?? 0,
            'cable_meters' => $get('cable_meters') ?? 0,
            'cable_unit_cost' => $get('cable_unit_cost') ?? 0,
            'trunking_meters' => $get('trunking_meters') ?? 0,
            'trunking_unit_cost' => $get('trunking_unit_cost') ?? 0,
            'installation_cost' => $get('installation_cost') ?? 0,
            'hdd_cost_per_tb' => $get('hdd_cost_per_tb') ?? 0,
            'poe_switch_unit_cost' => $get('poe_switch_unit_cost') ?? 0,
            'connector_unit_cost' => $get('connector_unit_cost') ?? 0,
            'markup_rate' => $get('markup_rate') ?? 0.6,
            'manual_items' => $get('manual_items') ?? [],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private static function requirementsPreview(Get $get): array
    {
        $requirements = self::previewCalculation($get)['calculation']['requirements'] ?? [];

        return [
            'NVR / DVR: '.($requirements['recorder'] ?? 'არ არის საჭირო'),
            'HDD: '.($requirements['storage'] ?? 'არ არის საჭირო'),
            'PoE სვიჩი: '.($requirements['poe_switch'] ?? 'არ არის საჭირო'),
            'RJ45 / BNC: '.($requirements['connectors'] ?? 'არ არის საჭირო'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function pricingPreview(Get $get): array
    {
        $calculation = self::previewCalculation($get);

        return [
            'თვითღირებულება: '.self::formatMoney((float) ($calculation['cost_total'] ?? 0)),
            'ფასნამატი: '.self::formatMoney((float) ($calculation['markup_total'] ?? 0)),
            'საბოლოო ფასი: '.self::formatMoney((float) ($calculation['final_total'] ?? 0)),
            'მოგება: '.self::formatMoney((float) ($calculation['profit_total'] ?? 0)),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function storagePreview(Get $get): array
    {
        $calculation = self::previewCalculation($get);
        $derived = $calculation['calculation']['derived'] ?? [];

        return [
            'ბიტრეიტი თითო კამერაზე: '.number_format((float) ($derived['bitrate_per_camera_mbps'] ?? 0), 2).' Mbps',
            'საჭირო HDD: '.number_format((float) ($calculation['required_storage_tb'] ?? 0), 1).' TB',
            'ჩაწერის პროფილი: '.($get('recording_hours_per_day') ?? 24).' სთ/დღე x '.($get('recording_days') ?? 30).' დღე',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function pdfLinePreview(Get $get): array
    {
        $lineItems = self::previewCalculation($get)['calculation']['line_items'] ?? [];

        if ($lineItems === []) {
            return ['ჯერ არ არის საკმარისი მონაცემი ხაზების ასაგებად.'];
        }

        return array_map(
            fn (array $item): string => sprintf(
                '%s: %s %s x %s = %s',
                $item['label'],
                self::formatQuantity((float) $item['quantity']),
                $item['unit'],
                self::formatMoney((float) $item['sell_unit']),
                self::formatMoney((float) $item['sell_total']),
            ),
            $lineItems,
        );
    }

    private static function formatMoney(float $amount): string
    {
        return '₾'.number_format($amount, 2, '.', ',');
    }

    private static function formatQuantity(float $quantity): string
    {
        return fmod($quantity, 1.0) === 0.0
            ? number_format($quantity, 0, '.', ',')
            : number_format($quantity, 2, '.', ',');
    }
}
