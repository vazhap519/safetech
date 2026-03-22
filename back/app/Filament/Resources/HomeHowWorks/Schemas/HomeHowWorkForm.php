<?php

namespace App\Filament\Resources\HomeHowWorks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class HomeHowWorkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('სათაური')
                    ->required(),

                TextInput::make('description')
                    ->label('აღწერა')
                    ->required(),

                Repeater::make('how_cards')
    ->label('ქარდები')
    ->schema([
        TextInput::make('title')
            ->label('სათაური')
            ->required(),

        TextInput::make('description')
            ->label('აღწერა')
            ->required(),

        Select::make('icon')
            ->label('აირჩიე იკონი')
            ->options([
                '📞' => '📞 ტელეფონი',
                '📝' => '📝 დოკუმენტი',
                '✅' => '✅ დასრულება',
            ])
            ->required(),

        Select::make('card_number')
            ->label('აირჩიე ნომერი card-ის')
            ->options([
                '1' => '1',
                '2' => '2',
                '3' => '3',
            ])
            ->required(),
    ])
    ->columns(2)
    ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
    ->required(),
                
                    ]);
    }
}