<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\{
    TextInput,
    Select,
    Toggle,
    Repeater,
    RichEditor,
    Textarea
};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | MAIN INFO
                |--------------------------------------------------------------------------
                */
                Section::make('Main Info')
                    ->schema([

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Short Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        // 🔥 CATEGORY (AUTO CREATE)
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([

                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                    $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                            ]),

                        // 🔥 AUTHOR (AUTO CREATE)
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([

                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                    $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                TextInput::make('email')
                                    ->email(),

                                Textarea::make('bio')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                SpatieMediaLibraryFileUpload::make('avatar')
                                    ->collection('avatar')
                                    ->image(),

                            ]),

                        Toggle::make('is_published')
                            ->default(true),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | MEDIA
                |--------------------------------------------------------------------------
                */
                Section::make('Media')
                    ->schema([

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->collection('cover')
                            ->image()
                            ->required(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | CONTENT SECTIONS
                |--------------------------------------------------------------------------
                */
                Section::make('Content Sections')
                    ->schema([

                        Repeater::make('sections')
                            ->relationship()
                            ->schema([

                                TextInput::make('title')
                                    ->label('Section Title')
                                    ->maxLength(255),

                                RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),

//                                TextInput::make('order')
//                                    ->numeric()
//                                    ->default(0),

                            ])
                            ->orderColumn('position') // ✅ სწორ column-ზე
                            ->reorderable()           // ✅ drag & drop მუშაობს
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */
                Section::make('SEO')
                    ->schema([

                        TextInput::make('meta_title')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}
