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
    Textarea,
    DateTimePicker
};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class PostForm
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
                ? Textarea::make("translations.fields.{$field}.{$locale}")->rows(3)
                : TextInput::make("translations.fields.{$field}.{$locale}")
            )->label("{$label} ({$localeLabel})"),
        )->values()->all();
    }

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

                                TextInput::make('translations.fields.name.en')
                                    ->label('Name (EN)'),

                                TextInput::make('translations.fields.name.ru')
                                    ->label('Name (RU)'),

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

                                TextInput::make('translations.fields.name.en')
                                    ->label('Name (EN)'),

                                TextInput::make('translations.fields.name.ru')
                                    ->label('Name (RU)'),

                                Textarea::make('translations.fields.bio.en')
                                    ->label('Bio (EN)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('translations.fields.bio.ru')
                                    ->label('Bio (RU)')
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
                            ->imageEditor()
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

                                TextInput::make('translations.fields.title.en')
                                    ->label('Section title (EN)'),
                                TextInput::make('translations.fields.title.ru')
                                    ->label('Section title (RU)'),
                                RichEditor::make('translations.fields.content.en')
                                    ->label('Section content (EN)')
                                    ->columnSpanFull(),
                                RichEditor::make('translations.fields.content.ru')
                                    ->label('Section content (RU)')
                                    ->columnSpanFull(),

                                TextInput::make('position')
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
                            ->maxLength(255),

                        Textarea::make('meta_description')
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
                            ->label('FAQ rich results')
                            ->schema([
                                TextInput::make('question')->label('კითხვა')->required(),
                                Textarea::make('answer')->label('პასუხი')->required(),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('schema')
                            ->label('Custom Schema JSON-LD')
                            ->rows(10)
                            ->dehydrateStateUsing(function ($state) {
                                if (! $state) return null;

                                $decoded = json_decode($state, true);

                                return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                            })
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                : $state)
                            ->columnSpanFull(),

                    ]),

                Section::make('Content and SEO in 3 languages')
                    ->schema([
                        ...self::translationInputs('title', 'Title'),
                        ...self::translationInputs('excerpt', 'Excerpt', true),
                        ...self::translationInputs('metaTitle', 'Meta title'),
                        ...self::translationInputs('metaDescription', 'Meta description', true),
                        Repeater::make('translations.keywords.ka')
                            ->label('Keywords (ქართული)')
                            ->simple(TextInput::make('value')),
                        Repeater::make('translations.keywords.en')
                            ->label('Keywords (English)')
                            ->simple(TextInput::make('value')),
                        Repeater::make('translations.keywords.ru')
                            ->label('Keywords (Русский)')
                            ->simple(TextInput::make('value')),
                    ])
                    ->columns(3),

            ]);
    }
}
