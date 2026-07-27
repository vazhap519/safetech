<?php

namespace App\Filament\Support;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

final class CategoryFields
{
    public static function core(bool $withAppearance = false): Section
    {
        return Section::make('Category')
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(StableSlug::syncOnCreate()),
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
}
