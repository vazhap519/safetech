<?php

namespace App\Filament\Resources\FaqSections\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class FaqSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              TextInput::make('title')
              ->required()
              ->label('სათაური'),
              TextInput::make('description')
              ->required()
              ->label('აღწერა'),
              Repeater::make('faq')
                ->schema([
                     TextInput::make('question')
              ->required()
              ->label('კითხვა'),
              TextInput::make('answer')
              ->required()
              ->label('პასუხი'),
                ]),
            ]);
    }
}
