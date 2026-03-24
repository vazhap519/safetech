<?php

namespace App\Filament\Resources\PrivacyPolicies\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
class PrivacyPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('სათაური')
                    ->required()->maxLength(255),
                TextInput::make('highlight')
                    ->label('მოკლე აღწერა')
                    ->required()->maxLength(255),

                RichEditor::make('content')
                    ->label('კონტენტი')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
