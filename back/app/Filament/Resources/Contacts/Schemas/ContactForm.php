<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('სახელი')
                    ->disabled(),

                TextInput::make('phone')
                    ->label('ტელეფონი')
                    ->disabled(),

                Textarea::make('message')
                    ->label('მესიჯი')
                    ->rows(6)
                    ->disabled(),

            ]);
    }
}
