<?php

namespace App\Filament\Resources\PrivacyPolicies\Schemas;

use App\Filament\Support\LocalizedContentFields;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrivacyPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('კონფიდენციალურობის პოლიტიკა')
                ->schema([
                    TextInput::make('title')
                        ->label('სათაური')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('highlight')
                        ->label('მოკლე აღწერა')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('content')
                        ->label('კონტენტი')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('კონფიდენციალურობის ტექსტი 3 ენაზე')
                ->schema([
                    ...LocalizedContentFields::inputs('title', 'სათაური'),
                    ...LocalizedContentFields::inputs('highlight', 'მოკლე აღწერა'),
                    ...LocalizedContentFields::inputs('content', 'კონტენტი', textarea: true, rows: 5),
                ])
                ->columns(3),
        ]);
    }
}
