<?php

namespace App\Filament\Support;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

final class CategoryFields
{
    public static function core(bool $withAppearance = false): Section
    {
        return Section::make('კატეგორია')
            ->schema([
                TextInput::make('name')
                    ->label('დასახელება')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(
                        fn ($state, callable $set) => $set('slug', Str::slug((string) $state)),
                    ),
                TextInput::make('slug')
                    ->label('URL კოდი')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ...($withAppearance ? [
                    ColorPicker::make('color')
                        ->label('ფერი')
                        ->default('#00C2A8'),
                    TextInput::make('icon')
                        ->label('აიკონი')
                        ->helperText('შეიყვანეთ emoji ან გამოყენებული აიკონის კლასი.'),
                    TextInput::make('sort_order')
                        ->label('რიგითობა')
                        ->numeric()
                        ->default(0),
                ] : []),
            ])
            ->columns(2);
    }
}
