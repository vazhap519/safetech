<?php

namespace App\Filament\Resources\Services\Schemas;


use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;


use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 🔵 HERO SECTION
                Section::make('ძირითადი ინფორმაცია')
                    ->schema([

                        TextInput::make('title')
                            ->label('სათაური')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // 👉 მხოლოდ მაშინ დააგენერიროს თუ slug ცარიელია
                                if (!$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('აღწერა')
                            ->required()
                            ->rows(4),

                        TextInput::make('phone')
                            ->label('ტელეფონი')
                            ->tel(),

                        TextInput::make('button_text')
                            ->label('ღილაკის ტექსტი')
                            ->default('დაგვიკავშირდი'),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('სურათი')
                            ->collection('services')
                            ->image()
                            ->imageEditor()
                            ->required(),

                    ])
                    ->columns(2),

                // 🟢 FEATURES
                Section::make('სერვისის უპირატესობები')
                    ->schema([

                        Repeater::make('features')
                            ->label('Features')
                            ->schema([
                                TextInput::make('text')
                                    ->label('ტექსტი')
                                    ->required(),
                            ])
                            ->defaultItems(3)
                            ->reorderable()
                            ->collapsible()
                            ->columns(1),

                    ]),

                // 🟡 SEO TEXT
                Section::make('SEO ტექსტი')
                    ->schema([

                        Textarea::make('seo_text')
                            ->label('SEO ტექსტი')
                            ->rows(6)
                            ->placeholder('SEO ტექსტი Google-ისთვის...'),

                    ]),

                // 🔴 FAQ
                Section::make('FAQ')
                    ->schema([

                        Repeater::make('faq')
                            ->label('FAQ')
                            ->schema([
                                TextInput::make('q')
                                    ->label('კითხვა')
                                    ->required(),

                                Textarea::make('a')
                                    ->label('პასუხი')
                                    ->required(),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->columns(1),

                    ]),
            ]);
    }
}