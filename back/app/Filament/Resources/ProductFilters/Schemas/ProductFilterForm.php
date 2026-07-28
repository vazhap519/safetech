<?php

namespace App\Filament\Resources\ProductFilters\Schemas;

use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\StableSlug;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductFilterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Filter group')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(StableSlug::syncOnCreate()),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
            Section::make('Translations')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'Filter name'),
                ])
                ->columns(3),
            Section::make('Options')
                ->schema([
                    Repeater::make('options')
                        ->label('Filter options')
                        ->schema([
                            TextInput::make('label')
                                ->label('Label')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (mixed $state, Set $set, Get $get): void {
                                    if (filled($get('slug'))) {
                                        return;
                                    }

                                    $slug = Str::slug((string) $state) ?: 'option';
                                    $set('slug', $slug);
                                }),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            TextInput::make('sort_order')
                                ->label('Sort order')
                                ->numeric()
                                ->default(0),
                            TextInput::make('ka')
                                ->label('Georgian'),
                            TextInput::make('en')
                                ->label('English'),
                            TextInput::make('ru')
                                ->label('Russian'),
                        ])
                        ->columns(2)
                        ->default([])
                        ->collapsible()
                        ->reorderable(),
                ]),
        ]);
    }
}
