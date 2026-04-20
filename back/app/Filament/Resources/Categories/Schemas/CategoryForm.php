<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),

                Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Meta title')
                            ->maxLength(70),

                        Forms\Components\Textarea::make('seo_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(180)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('seo_keywords')
                            ->label('Keywords')
                            ->schema([
                                Forms\Components\TextInput::make('value')->required(),
                            ])
                            ->default([])
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('intro_text')
                            ->label('Category intro text')
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('faq')
                            ->label('FAQ')
                            ->schema([
                                Forms\Components\TextInput::make('question')->required(),
                                Forms\Components\Textarea::make('answer')->required(),
                            ])
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('schema')
                            ->label('Custom schema JSON')
                            ->rows(8)
                            ->dehydrateStateUsing(function ($state) {
                                if (!$state) {
                                    return null;
                                }

                                $decoded = json_decode($state, true);

                                return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                            })
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                : $state)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('noindex')
                            ->label('Noindex')
                            ->helperText('Use for thin or temporary category pages.'),
                    ])
                    ->columns(2),
            ]);
    }
}
