<?php

namespace App\Filament\Support;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

final class CategoryFields
{
    public static function core(bool $withAppearance = false): Section
    {
        return Section::make('Category name (KA / EN / RU)')
            ->description('ახალი კატეგორიის შექმნისას შეავსეთ სამივე ენა. არსებულ ჩანაწერზე ცარიელი ინგლისური ან რუსული მნიშვნელობა უსაფრთხოდ გამოიყენებს ქართულ სათაურს.')
            ->schema([
                TextInput::make('name')
                    ->label('Category name (ქართული)')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(self::syncGeorgianNameAndSlug()),
                ...LocalizedContentFields::secondaryInputs(
                    'name',
                    'Category name',
                    maxLength: 255,
                    required: static fn (?Model $record): bool => $record === null,
                ),
                TextInput::make('slug')
                    ->label('URL slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ...($withAppearance ? [
                    ColorPicker::make('color')
                        ->label('Color')
                        ->default('#00C2A8'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options(AdminIconOptions::content())
                        ->searchable()
                        ->preload()
                        ->helperText('Choose an icon instead of typing it manually.'),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0),
                ] : []),
            ])
            ->columns(2);
    }

    private static function syncGeorgianNameAndSlug(): \Closure
    {
        return function (
            ?string $state,
            ?string $old,
            Get $get,
            Set $set,
            ?Model $record,
        ): void {
            $set('translations.fields.name.ka', $state);

            (StableSlug::syncOnCreate())($state, $old, $get, $set, $record);
        };
    }
}
