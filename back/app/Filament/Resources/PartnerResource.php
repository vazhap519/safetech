<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\Partner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationLabel = 'პარტნიორები';

    protected static ?string $modelLabel = 'პარტნიორი';

    protected static ?string $pluralModelLabel = 'პარტნიორები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('სახელი')
                ->required(),
            SpatieMediaLibraryFileUpload::make('logo')
                ->label('ლოგო')
                ->collection('logo')
                ->conversion('webp')
                ->image()
                ->imageEditor()
                ->maxSize(10240),
            TextInput::make('url')
                ->label('ვებგვერდი')
                ->url(),
            TextInput::make('category')->label('კატეგორია'),
            Toggle::make('is_active')
                ->label('აქტიური')
                ->default(true),
            TextInput::make('sort_order')
                ->label('რიგითობა')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('ლოგო')
                    ->getStateUsing(fn (Partner $record): ?string => $record->logo),
                TextColumn::make('name')->label('სახელი')->searchable(),
                TextColumn::make('category')->label('კატეგორია'),
                IconColumn::make('is_active')->label('აქტიური')->boolean(),
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
