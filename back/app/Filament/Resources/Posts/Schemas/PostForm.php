<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\{
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
                |------------------------------------------------------------------
                | 🟢 MAIN INFO
                |------------------------------------------------------------------
                */
                Section::make('Main Info')
                    ->schema([

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
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

                        /*
                        | CATEGORY
                        */
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        /*
                        | AUTHOR
                        */
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_published')
                            ->default(true),

                    ])
                    ->columns(2),

                /*
                |------------------------------------------------------------------
                | 🟣 MEDIA
                |------------------------------------------------------------------
                */
                Section::make('Media')
                    ->schema([

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->collection('cover')
                            ->image()
                            ->required(),

                    ]),

                /*
                |------------------------------------------------------------------
                | 🔵 CONTENT SECTIONS
                |------------------------------------------------------------------
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

                            ])
                            ->orderColumn('position') // ✅ FIX
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),

                    ]),

                /*
                |------------------------------------------------------------------
                | 🟡 SEO SYSTEM (🔥 CORE)
                |------------------------------------------------------------------
                */


Section::make('SEO (Google Optimization)')
    ->schema([

        /*
        |-------------------------------
        | 🔥 TITLE
        |-------------------------------
        */
        TextInput::make('seo.title')
            ->label('SEO Title')
            ->maxLength(255),

        /*
        |-------------------------------
        | 🔥 DESCRIPTION
        |-------------------------------
        */
        Textarea::make('seo.description')
            ->rows(3),

        /*
        |-------------------------------
        | 🔥 KEYWORDS
        |-------------------------------
        */
        Repeater::make('seo.keywords')
            ->simple(
                TextInput::make('value')
                    ->label('Keyword')
            )
            ->defaultItems(2),

        /*
        |-------------------------------
        | 🔥 SEO CONTENT
        |-------------------------------
        */
        Repeater::make('seo.content')
            ->schema([
                Textarea::make('text')
                    ->rows(3),
            ])
            ->defaultItems(2)
            ->columnSpanFull(),

        /*
        | 🔥 AI GENERATOR (FIXED FOR BLOG)
        */
        Action::make('generate_seo')
            ->label('🤖 Generate SEO')
            ->color('success')
            ->action(function ($set, $get) {

                $title = $get('title');
                $excerpt = $get('excerpt');

                if (!$title) return;

                /* =========================
                   🔥 BASIC SEO
                ========================= */
                $set('seo.title', $title . ' თბილისში');

                $set('seo.description',
                    ($excerpt ?: $title) . ' - დეტალური სტატია და რჩევები.'
                );

                $set('seo.keywords', [
                    ['value' => $title],
                    ['value' => $title . ' თბილისი'],
                    ['value' => 'IT ბლოგი'],
                ]);

                $set('seo.content', [
                    ['text' => $excerpt ?: $title],
                    ['text' => "{$title} თემაზე სრული გზამკვლევი."],
                ]);

                /* =========================
                   🔥 BLOG INTERNAL LINKS (CORRECT)
                ========================= */

                $links = [];

                // 🔹 keyword-based linking
                $keywords = collect([
                    $title,
                    $excerpt,
                ])->filter()->implode(' ');

                // 🔹 basic blog keywords (შეგიძლია გააფართოვო)
                $blogKeywords = [
                    'უსაფრთხოება',
                    'კამერები',
                    'ქსელები',
                    'IT',
                ];

                foreach ($blogKeywords as $word) {
                    if (str_contains(mb_strtolower($keywords), mb_strtolower($word))) {
                        $links[] = [
                            'keyword' => $word,
                            'url' => "/blog?search=" . urlencode($word),
                        ];
                    }
                }

                // 🔹 fallback
                if (empty($links)) {
                    $links = [
                        ['keyword' => 'ბლოგი', 'url' => '/blog'],
                    ];
                }

                $set('seo.internal_links', $links);
            }),

        /*
        | 📊 SEO SCORE
        */
        Placeholder::make('seo_score')
            ->content(function ($get) {

                $score = 0;

                if (strlen($get('seo.title')) >= 40) $score += 30;
                if (strlen($get('seo.description')) >= 120) $score += 30;
                if (!empty($get('seo.keywords'))) $score += 20;
                if (!empty($get('seo.content'))) $score += 20;

                return "SEO Score: {$score}/100";
            })
            ->reactive(),

        /*
        | 🔍 GOOGLE PREVIEW (🔥 ეს გაკლდა)
        */
        View::make('filament.seo-preview')
            ->reactive(),

    ])
    ->columnSpanFull(),

            ]);
    }
}
