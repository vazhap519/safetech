<?php
//
//namespace App\Filament\Resources\Services\Schemas;
//
//use Filament\Actions\Action;
//use Filament\Schemas\Schema;
//use Filament\Schemas\Components\Section;
//use Filament\Schemas\Components\View;
//
//use Filament\Forms\Components\TextInput;
//use Filament\Forms\Components\Textarea;
//use Filament\Forms\Components\Repeater;
//use Filament\Forms\Components\Placeholder;
//use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
//
//use Illuminate\Support\Str;
//
//class ServiceForm
//{
//    public static function configure(Schema $schema): Schema
//    {
//        return $schema->components([
//
//            /* =========================
//               🔵 MAIN INFO
//            ========================= */
//            Section::make('ძირითადი ინფორმაცია')
//                ->schema([
//
//                    TextInput::make('title')
//                        ->label('სათაური')
//                        ->required()
//                        ->live(onBlur: true)
//                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
//
//                            if ($get('slug')) return;
//
//                            $set('slug', Str::slug($state));
//                        }),
//
//                    TextInput::make('slug')
//                        ->label('Slug')
//                        ->required()
//                        ->unique(ignoreRecord: true),
//
//                    Textarea::make('description')
//                        ->label('აღწერა')
//                        ->required()
//                        ->rows(4),
//
//                    TextInput::make('phone')
//                        ->label('ტელეფონი')
//                        ->tel(),
//
//                    TextInput::make('button_text')
//                        ->label('ღილაკის ტექსტი')
//                        ->default('დაგვიკავშირდი'),
//
//                    SpatieMediaLibraryFileUpload::make('image')
//                        ->label('სურათი')
//                        ->collection('services')
//                        ->image()
//                        ->imageEditor()
//                        ->required(),
//
//                ])
//                ->columns(2),
//
//            /* =========================
//               🟢 FEATURES
//            ========================= */
//            Section::make('სერვისის უპირატესობები')
//                ->schema([
//
//                    Repeater::make('features')
//                        ->schema([
//                            TextInput::make('text')
//                                ->label('ტექსტი')
//                                ->required(),
//                        ])
//                        ->defaultItems(3)
//                        ->reorderable()
//                        ->collapsible(),
//
//                ]),
//
//            /* =========================
//               🔴 FAQ
//            ========================= */
//            Section::make('FAQ')
//                ->schema([
//
//                    Repeater::make('faq')
//                        ->schema([
//                            TextInput::make('q')->required(),
//                            Textarea::make('a')->required(),
//                        ])
//                        ->reorderable()
//                        ->collapsible(),
//
//                ]),
//
//            /* =========================
//      🟡 SEO SYSTEM (FINAL FIXED)
//   ========================= */
//            Section::make('SEO (Google Optimization)')
//                ->schema([
//
//                    /* 🔥 PAGE SELECT */
//                    Select::make('seo_page_key')
//                        ->label('Page')
//                        ->options(function () {
//
//                            return collect(Route::getRoutes())
//                                ->filter(function ($route) {
//
//                                    if (!in_array('GET', $route->methods())) return false;
//
//                                    $uri = $route->uri();
//
//                                    // ✅ მხოლოდ api routes
//                                    if (!str_starts_with($uri, 'api')) return false;
//
//                                    // ❌ dynamic
//                                    if (str_contains($uri, '{')) return false;
//
//                                    // ❌ unwanted
//                                    if (str_contains($uri, 'revalidate')) return false;
//                                    if (str_contains($uri, 'settings')) return false;
//                                    if (str_contains($uri, 'contact')) return false;
//                                    if (str_contains($uri, 'categories')) return false;
//
//                                    return true;
//                                })
//                                ->mapWithKeys(function ($route) {
//
//                                    $uri = $route->uri();
//
//                                    $key = preg_replace('#^api/?#', '', $uri);
//
//                                    if ($key === '' || $key === '/') {
//                                        $key = 'home';
//                                    }
//
//                                    return [
//                                        $key => str($key)->replace('-', ' ')->title()
//                                    ];
//                                })
//                                ->unique()
//                                ->sortKeys()
//                                ->toArray();
//                        })
//                        ->reactive()
//                        ->afterStateUpdated(function ($state, $set, $get) {
//
//                            $seo = $get('seo') ?? [];
//                            $pageData = $seo[$state] ?? [];
//
//                            $set('seo.title', $pageData['title'] ?? '');
//                            $set('seo.description', $pageData['description'] ?? '');
//                            $set('seo.keywords', $pageData['keywords'] ?? []);
//                            $set('seo.content', $pageData['content'] ?? []);
//                            $set('seo.internal_links', $pageData['internal_links'] ?? []);
//                        })
//                        ->required(),
//
//                    /* =========================
//                       SEO FIELDS
//                    ========================= */
//
//                    TextInput::make('seo.title')
//                        ->label('SEO Title')
//                        ->reactive()
//                        ->afterStateUpdated(fn ($state, $set, $get) =>
//                        self::updateSeo($set, $get, 'title', $state)
//                        ),
//
//                    Textarea::make('seo.description')
//                        ->rows(3)
//                        ->reactive()
//                        ->afterStateUpdated(fn ($state, $set, $get) =>
//                        self::updateSeo($set, $get, 'description', $state)
//                        ),
//
//                    Repeater::make('seo.keywords')
//                        ->simple(TextInput::make('value'))
//                        ->reactive()
//                        ->afterStateUpdated(fn ($state, $set, $get) =>
//                        self::updateSeo($set, $get, 'keywords', $state)
//                        ),
//
//                    Repeater::make('seo.content')
//                        ->schema([
//                            Textarea::make('text'),
//                        ])
//                        ->defaultItems(2)
//                        ->reactive()
//                        ->afterStateUpdated(fn ($state, $set, $get) =>
//                        self::updateSeo($set, $get, 'content', $state)
//                        ),
//
//                    Repeater::make('seo.internal_links')
//                        ->label('Internal Links')
//                        ->schema([
//                            TextInput::make('keyword')->required(),
//                            TextInput::make('url')->required(),
//                        ])
//                        ->columnSpanFull()
//                        ->reactive()
//                        ->afterStateUpdated(fn ($state, $set, $get) =>
//                        self::updateSeo($set, $get, 'internal_links', $state)
//                        ),
//
//                    /* =========================
//                       🤖 GENERATE SEO (FIXED)
//                    ========================= */
//                    Action::make('generate_seo')
//                        ->label('🤖 Generate SEO')
//                        ->color('success')
//                        ->action(function ($set, $get) {
//
//                            $page = $get('seo_page_key');
//
//                            if (!$page) return;
//
//                            $title = $get('seo.title') ?: ucfirst($page);
//                            $description = $get('seo.description') ?: 'პროფესიონალური სერვისი თბილისში.';
//
//                            $seo = $get('seo') ?? [];
//
//                            $seo[$page] = [
//                                'title' => $title . ' თბილისში',
//                                'description' => $description,
//                                'keywords' => [
//                                    ['value' => $title . ' თბილისი'],
//                                    ['value' => 'IT სერვისები თბილისი'],
//                                ],
//                                'content' => [
//                                    ['text' => $description],
//                                ],
//                                'internal_links' => [
//                                    [
//                                        'keyword' => $title,
//                                        'url' => '/services',
//                                    ],
//                                    [
//                                        'keyword' => 'ბლოგი',
//                                        'url' => '/blog',
//                                    ],
//                                ],
//                            ];
//
//                            $set('seo', $seo);
//
//                            $set('seo.title', $seo[$page]['title']);
//                            $set('seo.description', $seo[$page]['description']);
//                            $set('seo.keywords', $seo[$page]['keywords']);
//                            $set('seo.content', $seo[$page]['content']);
//                            $set('seo.internal_links', $seo[$page]['internal_links']);
//                        }),
//
//                    /* =========================
//                       📊 SEO SCORE (IMPROVED)
//                    ========================= */
//                    Placeholder::make('seo_score')
//                        ->content(function ($get) {
//
//                            $score = 0;
//
//                            $title = $get('seo.title') ?? '';
//                            $desc = $get('seo.description') ?? '';
//
//                            if (strlen($title) >= 40 && strlen($title) <= 60) $score += 30;
//                            if (strlen($desc) >= 120 && strlen($desc) <= 160) $score += 30;
//                            if (!empty($get('seo.keywords'))) $score += 20;
//                            if (!empty($get('seo.content'))) $score += 20;
//
//                            return "Score: {$score}/100";
//                        })
//                        ->reactive(),
//
//                    /* =========================
//                       🔍 GOOGLE PREVIEW
//                    ========================= */
//                    View::make('filament.seo-preview')
//                        ->reactive(),
//
//                ]),
//
//        ]);
//    }
//}


namespace App\Filament\Resources\Services\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /* =========================
               🔵 MAIN INFO
            ========================= */
            Section::make('ძირითადი ინფორმაცია')
                ->schema([

                    TextInput::make('title')
                        ->label('სათაური')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get, $old) {

                            $currentSlug = $get('slug');
                            $oldSlug = Str::slug($old);

                            // თუ slug ჯერ კიდევ ძველი auto იყო → განაახლე
                            if ($currentSlug === $oldSlug || empty($currentSlug)) {
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

            /* =========================
               🟢 FEATURES
            ========================= */
            Section::make('სერვისის უპირატესობები')
                ->schema([

                    Repeater::make('features')
                        ->schema([
                            TextInput::make('text')->required(),
                        ])
                        ->defaultItems(3),

                ]),

            /* =========================
               🔴 FAQ
            ========================= */
            Section::make('FAQ')
                ->schema([

                    Repeater::make('faq')
                        ->schema([
                            TextInput::make('q')->required(),
                            Textarea::make('a')->required(),
                        ]),

                ]),

            Section::make('სერვისის აღწერა')->schema([
                TextInput::make('short_description')->required(),
                Textarea::make('long_description')->required(),
            ]),
            Section::make('პრობლემები (მომხმარებლის ტკივილები)')
                ->schema([

                    Repeater::make('problems')
                        ->schema([
                            TextInput::make('text')
                                ->label('პრობლემა')
                                ->required(),
                        ])
                        ->defaultItems(3)
                        ->reorderable()
                        ->collapsible(),

                ]),

            Section::make('შედეგები (Results / Benefits)')
                ->schema([

                    Repeater::make('results')
                        ->schema([
                            TextInput::make('text')
                                ->label('შედეგი')
                                ->placeholder('+40% performance')
                                ->required(),
                        ])
                        ->defaultItems(3)
                        ->reorderable()
                        ->collapsible(),

                ]),
            Section::make('Case Study (ქეისი)')
                ->schema([

                    TextInput::make('case_study.title')
                        ->label('კომპანიის სახელი'),

                    Textarea::make('case_study.description')
                        ->label('აღწერა')
                        ->rows(3),

                    TextInput::make('case_study.result')
                        ->label('შედეგი (მაგ: -70% downtime)'),

                ])
                ->collapsible(),

            Section::make('Testimonials (კლიენტების შეფასებები)')
                ->schema([

                    Repeater::make('testimonials')
                        ->schema([
                            TextInput::make('name')
                                ->label('სახელი'),

                            Textarea::make('text')
                                ->label('კომენტარი')
                                ->rows(3),
                        ])
                        ->defaultItems(2)
                        ->reorderable()
                        ->collapsible(),

                ]),
            Section::make('CTA (Call To Action)')
                ->schema([

                    TextInput::make('cta_title')
                        ->label('CTA სათაური')
                        ->placeholder('დაგვიკავშირდი დღესვე'),

                    Textarea::make('cta_description')
                        ->label('CTA აღწერა')
                        ->rows(2),

                ])
                ->collapsible(),
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
//                    Action::make('generate_seo')
//                        ->label('🤖 Generate SEO')
//                        ->color('success')
//                        ->action(function ($set, $get) {
//
//                            $title = $get('title');
//                            $excerpt = $get('excerpt');
//
//                            if (!$title) return;
//
//                            /* =========================
//                               🔥 BASIC SEO
//                            ========================= */
//                            $set('seo.title', $title . ' თბილისში');
//
//                            $set('seo.description',
//                                ($excerpt ?: $title) . ' - დეტალური სტატია და რჩევები.'
//                            );
//
//                            $set('seo.keywords', [
//                                ['value' => $title],
//                                ['value' => $title . ' თბილისი'],
//                                ['value' => 'IT ბლოგი'],
//                            ]);
//
//                            $set('seo.content', [
//                                ['text' => $excerpt ?: $title],
//                                ['text' => "{$title} თემაზე სრული გზამკვლევი."],
//                            ]);
//
//                            /* =========================
//                               🔥 BLOG INTERNAL LINKS (CORRECT)
//                            ========================= */
//
//                            $links = [];
//
//                            // 🔹 keyword-based linking
//                            $keywords = collect([
//                                $title,
//                                $excerpt,
//                            ])->filter()->implode(' ');
//
//                            // 🔹 basic blog keywords (შეგიძლია გააფართოვო)
//                            $blogKeywords = [
//                                'უსაფრთხოება',
//                                'კამერები',
//                                'ქსელები',
//                                'IT',
//                            ];
//
//                            foreach ($blogKeywords as $word) {
//                                if (str_contains(mb_strtolower($keywords), mb_strtolower($word))) {
//                                    $links[] = [
//                                        'keyword' => $word,
//                                        'url' => "/blog?search=" . urlencode($word),
//                                    ];
//                                }
//                            }
//
//                            // 🔹 fallback
//                            if (empty($links)) {
//                                $links = [
//                                    ['keyword' => 'ბლოგი', 'url' => '/blog'],
//                                ];
//                            }
//
//                            $set('seo.internal_links', $links);
//                        }),
                    Action::make('generate_seo')
                        ->label('🤖 Generate SEO')
                        ->color('success')
                        ->action(function ($set, $get) {

                            $title = $get('title');
                            $long = $get('long_description');

                            if (!$title) return;

                            // 🔥 trim + cut (ძალიან მნიშვნელოვანია SEO-სთვის)
                            $description = $long
                                ? mb_substr(strip_tags($long), 0, 160)
                                : $title . ' - დეტალური ინფორმაცია.';

                            /* =========================
                               🔥 BASIC SEO
                            ========================= */
                            $set('seo.title', $title . ' თბილისში');

                            $set('seo.description', $description);

                            $set('seo.keywords', [
                                ['value' => $title],
                                ['value' => $title . ' თბილისი'],
                                ['value' => 'IT სერვისები'],
                            ]);

                            $set('seo.content', [
                                ['text' => $description],
                                ['text' => "{$title} სერვისის სრული გზამკვლევი."],
                            ]);

                            /* =========================
                               🔥 INTERNAL LINKS
                            ========================= */

                            $links = [];

                            $keywords = mb_strtolower($title . ' ' . $long);

                            $blogKeywords = [
                                'უსაფრთხოება',
                                'კამერები',
                                'ქსელები',
                                'IT',
                            ];

                            foreach ($blogKeywords as $word) {
                                if (str_contains($keywords, mb_strtolower($word))) {
                                    $links[] = [
                                        'keyword' => $word,
                                        'url' => "/blog?search=" . urlencode($word),
                                    ];
                                }
                            }

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
