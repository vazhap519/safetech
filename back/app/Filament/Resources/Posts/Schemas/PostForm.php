<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\StructuredDataJsonField;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                Section::make('ძირითადი ინფორმაცია')
                    ->schema([

                        TextInput::make('title')
                            ->label('სათაური')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('URL კოდი')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('მოკლე აღწერა')
                            ->rows(3)
                            ->columnSpanFull(),

                        // 🔥 CATEGORY (AUTO CREATE)
                        Select::make('category_id')
                            ->label('კატეგორია')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([

                                TextInput::make('name')
                                    ->label('დასახელება (ქართული)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->label('URL კოდი')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                TextInput::make('translations.fields.name.en')
                                    ->label('დასახელება (EN)'),

                                TextInput::make('translations.fields.name.ru')
                                    ->label('დასახელება (RU)'),

                            ]),

                        // 🔥 AUTHOR (AUTO CREATE)
                        Select::make('author_id')
                            ->label('ავტორი')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([

                                TextInput::make('name')
                                    ->label('სახელი (ქართული)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))
                                    ),

                                TextInput::make('slug')
                                    ->label('URL კოდი')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                TextInput::make('email')
                                    ->label('ელფოსტა')
                                    ->email(),

                                Textarea::make('bio')
                                    ->label('ბიოგრაფია (ქართული)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                TextInput::make('translations.fields.name.en')
                                    ->label('სახელი (EN)'),

                                TextInput::make('translations.fields.name.ru')
                                    ->label('სახელი (RU)'),

                                Textarea::make('translations.fields.bio.en')
                                    ->label('ბიოგრაფია (EN)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('translations.fields.bio.ru')
                                    ->label('ბიოგრაფია (RU)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                SpatieMediaLibraryFileUpload::make('avatar')
                                    ->label('ავატარი')
                                    ->collection('avatar')
                                    ->conversion('webp')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(10240),

                            ]),

                        Toggle::make('is_published')
                            ->label('გამოქვეყნებულია')
                            ->default(true),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | MEDIA
                |--------------------------------------------------------------------------
                */
                Section::make('მედია')
                    ->schema([

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('მთავარი სურათი')
                            ->collection('cover')
                            ->conversion('webp')
                            ->image()
                            ->imageEditor()
                            ->maxSize(10240)
                            ->required(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | CONTENT SECTIONS
                |--------------------------------------------------------------------------
                */
                Section::make('სტატიის სექციები')
                    ->schema([

                        Repeater::make('sections')
                            ->label('სექციები')
                            ->relationship()
                            ->schema([

                                TextInput::make('title')
                                    ->label('სექციის სათაური (ქართული)')
                                    ->maxLength(255),

                                RichEditor::make('content')
                                    ->label('სექციის ტექსტი (ქართული)')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('translations.fields.title.en')
                                    ->label('სექციის სათაური (EN)'),
                                TextInput::make('translations.fields.title.ru')
                                    ->label('სექციის სათაური (RU)'),
                                RichEditor::make('translations.fields.content.en')
                                    ->label('სექციის ტექსტი (EN)')
                                    ->columnSpanFull(),
                                RichEditor::make('translations.fields.content.ru')
                                    ->label('სექციის ტექსტი (RU)')
                                    ->columnSpanFull(),

                                TextInput::make('position')
                                    ->label('რიგითობა')
                                    ->numeric()
                                    ->default(0),

                            ])
                            ->orderColumn('position')
                            ->reorderable() // 🔥 drag & drop
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
                            ->label('SEO სათაური')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta აღწერა')
                            ->rows(3)
                            ->columnSpanFull(),

                        Repeater::make('seo_keywords')
                            ->label('საკვანძო სიტყვები')
                            ->simple(TextInput::make('value'))
                            ->default([]),

                        Toggle::make('noindex')
                            ->label('საძიებო სისტემებში არ გამოჩნდეს')
                            ->default(false),

                        TextInput::make('seo_author')
                            ->label('Schema ავტორი'),

                        DateTimePicker::make('seo_published_at')
                            ->label('Schema გამოქვეყნების თარიღი'),

                        Repeater::make('faq')
                            ->label('FAQ გაფართოებული შედეგებისთვის')
                            ->schema([
                                TextInput::make('question')->label('კითხვა')->required(),
                                Textarea::make('answer')->label('პასუხი')->required(),
                            ])
                            ->columnSpanFull(),

                        StructuredDataJsonField::make(
                            'ცარიელი დატოვეთ ავტომატური Article/FAQ schema-ს გამოსაყენებლად.',
                        ),

                    ]),

                Section::make('კონტენტი და SEO 3 ენაზე')
                    ->schema([
                        ...LocalizedContentFields::inputs('title', 'სათაური'),
                        ...LocalizedContentFields::inputs('excerpt', 'მოკლე აღწერა', textarea: true),
                        ...LocalizedContentFields::inputs('metaTitle', 'SEO სათაური'),
                        ...LocalizedContentFields::inputs('metaDescription', 'Meta აღწერა', textarea: true),
                        Repeater::make('translations.keywords.ka')
                            ->label('კონტენტის თემები (ქართული)')
                            ->simple(TextInput::make('value')),
                        Repeater::make('translations.keywords.en')
                            ->label('კონტენტის თემები (EN)')
                            ->simple(TextInput::make('value')),
                        Repeater::make('translations.keywords.ru')
                            ->label('კონტენტის თემები (RU)')
                            ->simple(TextInput::make('value')),
                    ])
                    ->columns(3),

            ]);
    }
}
