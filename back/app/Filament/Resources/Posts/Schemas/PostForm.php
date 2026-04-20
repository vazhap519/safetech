<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Support\SocialLinks;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\{
    DateTimePicker,
    TextInput,
    Select,
    Toggle,
    Repeater,
    RichEditor,
    Textarea,
    Placeholder
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
                | 🟢 MAIN INFO
                |--------------------------------------------------------------------------
                */
                Section::make('Main Info')
                    ->schema([

                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {

                                $set('slug', Str::slug($state));

                                // 🔥 update schema
                                $type = $get('schema_type') ?: 'Article';

                                $set('schema', self::generateSchema(
                                    $type,
                                    $state,
                                    $get('excerpt')
                                ));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Textarea::make('excerpt')
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, $set, $get) {

                                $type = $get('schema_type') ?: 'Article';

                                $set('schema', self::generateSchema(
                                    $type,
                                    $get('title'),
                                    $state
                                ));
                            }),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_published')
                            ->default(true),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | 🟣 MEDIA
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
                | 🔵 CONTENT
                |--------------------------------------------------------------------------
                */
                Section::make('Content Sections')
                    ->schema([

                        Repeater::make('sections')
                            ->relationship()
                            ->schema([
                                TextInput::make('title'),
                                RichEditor::make('content')->required(),
                            ])
                            ->orderColumn('position')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),

                    ]),
                /*
              |--------------------------------------------------------------------------
              | 🔥 FAQ
              |--------------------------------------------------------------------------
              */
                Section::make('FAQ')
                    ->schema([
                        Repeater::make('faq')
                            ->schema([
                                TextInput::make('question')->required(),
                                Textarea::make('answer')->required(),
                            ])
                            ->collapsible(),
                    ]),

                /*
                |--------------------------------------------------------------------------
                | 🟡 SEO (FIXED 100%)
                |--------------------------------------------------------------------------
                */
                Section::make('SEO')
                    ->schema([

                        TextInput::make('seo.title')
                            ->label('Meta Title')
                            ->maxLength(60),

                        Textarea::make('seo.description')
                            ->label('Meta Description')
                            ->maxLength(160),

                        Repeater::make('seo.keywords')
                            ->label('Keywords')
                            ->schema([
                                TextInput::make('value')->required(),
                            ])
                            ->default([]),

                        /*
                        | 🔥 SCHEMA TYPE (FIXED)
                        */
                        Select::make('schema_type')
                            ->label('Schema Type')
                            ->options([
                                'Article' => 'Article',
                                'WebPage' => 'WebPage',
                                'Service' => 'Service',
                            ])
                            ->default('Article') // ✅ CRITICAL FIX
                            ->required()
                            ->reactive()

                            ->afterStateUpdated(function ($state, callable $set, callable $get) {

                                $type = $state ?: 'Article';

                                $set('schema', self::generateSchema(
                                    $type,
                                    $get('title'),
                                    $get('excerpt')
                                ));
                            })

                            ->afterStateHydrated(function ($state, callable $set, callable $get) {

                                if ($get('schema')) return;

                                $type = $state ?: 'Article';

                                $set('schema', self::generateSchema(
                                    $type,
                                    $get('title'),
                                    $get('excerpt')
                                ));
                            }),

                        /*
                        | 🔥 SCHEMA JSON
                        */
                        Textarea::make('schema')
                            ->label('Schema JSON (JSON-LD)')
                            ->rows(12)
                            ->helperText('Auto-generated, editable')

                            ->dehydrateStateUsing(function ($state) {
                                if (!$state) return null;

                                $decoded = json_decode($state, true);

                                return json_last_error() === JSON_ERROR_NONE
                                    ? $decoded
                                    : null;
                            })

                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                }
                                return $state;
                            }),

                        /*
                        | 🔥 SEO GENERATOR
                        */
                        Action::make('generate_seo')
                            ->label('Generate SEO')
                            ->action(function ($set, $get) {

                                $title = $get('title');

                                if (!$title) return;

                                $set('seo.title', $title . ' | ' . config('app.name'));

                                $set('seo.description',
                                    Str::limit($get('excerpt') ?? $title, 150)
                                );

                                $set('seo.keywords', [
                                    ['value' => $title],
                                    ['value' => 'blog'],
                                    ['value' => 'article'],
                                ]);
                            }),

                        Placeholder::make('seo_score')
                            ->content(fn ($get) =>
                            $get('seo.title') && $get('seo.description')
                                ? '✅ Good SEO'
                                : '❌ Missing SEO'
                            ),

                        View::make('filament.seo-preview'),

                    ]),



                /*
                |--------------------------------------------------------------------------
                | ⚙️ ADVANCED
                |--------------------------------------------------------------------------
                */
                Section::make('Advanced')
                    ->schema([

                        TextInput::make('seo_author'),

                        DateTimePicker::make('seo_published_at'),

                    ]),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 SCHEMA GENERATOR
    |--------------------------------------------------------------------------
    */
    protected static function generateSchema($type, $title = null, $description = null): ?string
    {
        $baseUrl = SocialLinks::frontendUrl('/');
        $name = config('app.name');

        return match ($type) {

            'BlogPosting' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "BlogPosting",
                "headline" => $title,
                "description" => $description,
                "image" => $baseUrl . "/images/placeholder.jpg",
                "author" => [
                    "@type" => "Organization",
                    "name" => $name
                ],
                "publisher" => [
                    "@type" => "Organization",
                    "name" => $name
                ],
                "mainEntityOfPage" => $baseUrl,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'Article' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "Article",
                "headline" => $title,
                "description" => $description,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            default => json_encode([
                "@context" => "https://schema.org",
                "@type" => "WebPage",
                "name" => $title,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        };
    }
}
