<?php

namespace App\Filament\Resources\HomeHeroSections\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class HomeHeroSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('HomeHeroSectionTexts')
                    ->schema([
                        TextInput::make('home_hero_title')
                            ->label('სათაური')
                            ->required(),

                        Textarea::make('home_hero_description')
                            ->label('აღწერა')
                            ->required(),
                    ])
                    ->label('Hero სექციის ტექტები'),

                Section::make('HomeHeroSectionButtonsTexts')
                    ->schema([
                        TextInput::make('home_hero_call_button_text')
                            ->label('დარეკვის ღილაკის ტექსტი')
                            ->required(),

                        TextInput::make('home_hero_call_button_number')
                            ->label('ტელეფონის ნომერი')
                            ->tel()
                            ->inputMode('numeric')
                            ->maxLength(15)
                            ->required()
                            ->formatStateUsing(fn ($state) => HomeHeroSectionForm::formatPhone($state))
                            ->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state)),

                        Textarea::make('home_hero_service_button_text')
                            ->label('სერვისების ღილაკის ტექსტი')
                            ->required(),
                    ])
                    ->label('Hero სექციის ღილაკების ტექსტები'),

                Repeater::make('home_hero_list')
                    ->schema([
                        TextInput::make('text')
                            ->label('სიის ტექსტი')
                            ->required(),
                    ])
                    ->label('Hero სექციის სიის ტექსტები'),

                SpatieMediaLibraryFileUpload::make('hero_image')
                    ->collection('hero')
                    ->image()
                    ->imageEditor()
                    ->required(),
            ]);
    }

    public static function formatPhone($value)
    {
        if (!$value) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        // +995 ფორმატი (Georgia)
        if (strlen($digits) === 12 && str_starts_with($digits, '995')) {
            return '+' . substr($digits, 0, 3) . ' ' .
                substr($digits, 3, 3) . ' ' .
                substr($digits, 6, 3) . ' ' .
                substr($digits, 9);
        }

        // 599123456 → 599 123 456
        if (strlen($digits) === 9) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $digits);
        }

        return $digits;
    }
}