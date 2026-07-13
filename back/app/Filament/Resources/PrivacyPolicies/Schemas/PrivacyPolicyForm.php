<?php

namespace App\Filament\Resources\PrivacyPolicies\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrivacyPolicyForm
{
    /** @return array<int, TextInput|Textarea> */
    private static function translationInputs(string $field, string $label, bool $textarea = false): array
    {
        return collect([
            'ka' => 'ქართული',
            'en' => 'English',
            'ru' => 'Русский',
        ])->map(
            fn (string $localeLabel, string $locale) => ($textarea
                ? Textarea::make("translations.fields.{$field}.{$locale}")->rows(5)
                : TextInput::make("translations.fields.{$field}.{$locale}")
            )->label("{$label} ({$localeLabel})"),
        )->values()->all();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Privacy Policy')
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

            Section::make('Privacy content in 3 languages')
                ->schema([
                    ...self::translationInputs('title', 'Title'),
                    ...self::translationInputs('highlight', 'Highlight'),
                    ...self::translationInputs('content', 'Content', true),
                ])
                ->columns(3),
        ]);
    }
}
