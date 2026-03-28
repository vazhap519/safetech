<?php

namespace App\Filament\Resources\HomeStats\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HomeStatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')->default(true),

                Repeater::make('items')
                    ->label('სტატისტიკა')
                    ->schema([
                        TextInput::make('value')->required(),
                        TextInput::make('label')->required(),
                    ])
                    ->defaultItems(3),
            ]);
    }
}
