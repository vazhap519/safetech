<?php

namespace App\Filament\Resources\HomeWhyUs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class HomeWhyUsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('why_us_title')->label('სათაური'),
                 TextInput::make('why_us_description')
                 ->label('აღწერა'),
                Repeater::make('why_us_items')
                    ->schema([
                        TextInput::make('title')->label('სათაური')->required(),
                        TextInput::make('description')->label('აღწერა')->required(),
                        



                        Select::make('icon')
    ->label('აირჩიე იკონი')
    ->options([
        '⚡' => '⚡ სწრაფი მომსახურება',
        '🛠' => '🛠 გამოცდილება',
        '💯' => '💯 მაღალი ხარისხი',
        '📍' => '📍 ლოკაცია',
    ])
    ->searchable()
    ->required()
                    ]),                

            ]);
    }
}
