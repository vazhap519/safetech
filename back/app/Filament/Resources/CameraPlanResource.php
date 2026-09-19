<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CameraPlanResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\CameraPlan;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CameraPlanResource extends Resource
{
    protected static ?string $model = CameraPlan::class;

    protected static ?string $navigationLabel = 'კამერების გეგმები / მოთხოვნები';

    protected static ?string $modelLabel = 'კამერის გეგმა';

    protected static ?string $pluralModelLabel = 'კამერების გეგმები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Services;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 11;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ობიექტი და კლიენტი')->schema([
                TextInput::make('title')->label('ობიექტი')->required(),
                TextInput::make('contact_name')->label('კლიენტი')->required(),
                TextInput::make('contact_phone')->label('ტელეფონი')->required(),
                TextInput::make('contact_email')->label('ელფოსტა')->email(),
                Select::make('status')->label('სტატუსი')->options([
                    'new' => 'ახალი', 'reviewing' => 'მუშავდება',
                    'quoted' => 'შეთავაზებულია', 'closed' => 'დახურული',
                ])->required(),
            ])->columns(2),
            Section::make('დაცული გეგმა')->schema([
                \Filament\Forms\Components\Placeholder::make('planner_link')
                    ->label('ფონის ფოტოს სანახავად / პროექტის JSON ფაილი')
                    ->content(fn (?CameraPlan $record) => $record
                        ? new \Illuminate\Support\HtmlString(
                            '<a target="_blank" rel="noopener" href="'.e(route('admin.camera-plans.layout', $record)).'">გეგმის JSON</a>'
                            .($record->background_path
                                ? ' · <a target="_blank" rel="noopener" href="'.e(route('admin.camera-plans.background', $record)).'">ობიექტის ფოტო</a>'
                                : '')
                        ) : '—'),
                Textarea::make('layout_preview')->label('პარამეტრები')
                    ->formatStateUsing(fn (?CameraPlan $record) => $record
                        ? json_encode($record->layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        : '')
                    ->readOnly()->dehydrated(false)->rows(12),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('ობიექტი')->searchable(),
                TextColumn::make('contact_name')->label('კლიენტი')->searchable(),
                TextColumn::make('contact_phone')->label('ნომერი'),
                TextColumn::make('status')->label('სტატუსი')->badge(),
                TextColumn::make('created_at')->label('მიღებულია')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCameraPlans::route('/'),
            'edit' => Pages\EditCameraPlan::route('/{record}/edit'),
        ];
    }
}
