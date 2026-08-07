<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutPageResource\Pages;
use App\Filament\Support\AboutPageTranslationFields;
use App\Filament\Support\NavigationGroup;
use App\Models\SiteSetting;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AboutPageResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationLabel = 'ჩვენს შესახებ';

    protected static ?string $modelLabel = 'ჩვენს შესახებ გვერდი';

    protected static ?string $pluralModelLabel = 'ჩვენს შესახებ';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Pages;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            ...AboutPageTranslationFields::sections(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('გვერდი')
                    ->formatStateUsing(fn (): string => 'ჩვენს შესახებ'),
                TextColumn::make('updated_at')
                    ->label('ბოლო განახლება')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('რედაქტირება'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('key', 'translations');
    }

    public static function canCreate(): bool
    {
        return SiteSetting::query()
            ->where('key', 'translations')
            ->doesntExist();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutPageSettings::route('/'),
            'create' => Pages\CreateAboutPageSetting::route('/create'),
            'edit' => Pages\EditAboutPageSetting::route('/{record}/edit/{section?}'),
        ];
    }
}
