<?php



namespace App\Filament\Resources\Services\Schemas;

use App\Support\SocialLinks;
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
use Filament\Forms\Components\Toggle;

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

                    Select::make('category_for_service_id')
                        ->label('კატეგორია')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('title')
                        ->label('სათაური')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone')
                        ->label('ტელეფონი')
                        ->tel(),

                    TextInput::make('button_text')
                        ->label('ღილაკის ტექსტი')
                        ->default('დაგვიკავშირდი'),

                    SpatieMediaLibraryFileUpload::make('services') // ⬅️ ეს შეცვალე cover-დან
                    ->label('სურათი')
                        ->collection('services')
                        ->image()
                        ->required(),

                ])
                ->columns(2),

            /* =========================
               🟢 TEXT CONTENT
            ========================= */
            Section::make('ტექსტი')
                ->schema([

                    TextInput::make('short_description')
                        ->label('მოკლე ტექსტი')
                        ->required(),

                    Textarea::make('long_description')
                        ->label('დეტალური აღწერა')
                        ->rows(5),

                ]),

            /* =========================
               🟡 FEATURES (MAX 5)
            ========================= */
            Section::make('რატომ ჩვენ?')
                ->schema([

                    Repeater::make('features')
                        ->schema([
                            TextInput::make('text')
                                ->label('Feature')
                                ->required(),
                        ])
                        ->defaultItems(3)
                        ->maxItems(5),

                ]),

            /* =========================
               🔴 FAQ (OPTIONAL)
            ========================= */
            Section::make('FAQ')
                ->schema([

                    Repeater::make('faq')
                        ->schema([
                            TextInput::make('q')
                                ->label('Question')
                                ->required(),

                            Textarea::make('a')
                                ->label('Answer')
                                ->required(),
                        ])
                        ->collapsed(),

                ]),

            /* =========================
               🔥 CTA (CRITICAL)
            ========================= */
            Section::make('CTA')
                ->schema([

                    TextInput::make('cta_title')
                        ->label('CTA სათაური')
                        ->placeholder('დაგვიკავშირდი დღესვე'),

                    Textarea::make('cta_description')
                        ->label('CTA აღწერა')
                        ->rows(2),

                ]),

            /* =========================
               🔍 SEO (UNCHANGED)
            ========================= */
            Section::make('SEO (Google Optimization)')
                ->schema([

                    TextInput::make('seo.title')
                        ->label('SEO Title')
                        ->maxLength(255),

                    Toggle::make('seo.noindex')
                        ->label('Noindex')
                        ->default(false),

                    Textarea::make('seo.description')
                        ->rows(3),

                    Repeater::make('seo.keywords')
                        ->simple(
                            TextInput::make('value')
                                ->label('Keyword')
                        )
                        ->defaultItems(2),

                    Repeater::make('seo.content')
                        ->schema([
                            Textarea::make('text')
                                ->rows(3),
                        ])
                        ->defaultItems(2)
                        ->columnSpanFull(),

                    Action::make('generate_seo')
                        ->label('🤖 Generate SEO')
                        ->color('success')
                        ->action(function ($set, $get) {

                            $title = $get('title');
                            $long = $get('long_description');

                            if (!$title) return;

                            $description = $long
                                ? mb_substr(strip_tags($long), 0, 160)
                                : $title . ' - დეტალური ინფორმაცია.';

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

                            $links = [];

                            $keywords = mb_strtolower($title . ' ' . $long);

                            $serviceKeywords = [
                                'უსაფრთხოება',
                                'კამერები',
                                'ქსელები',
                                'IT',
                            ];

                            foreach ($serviceKeywords as $word) {
                                if (str_contains($keywords, mb_strtolower($word))) {
                                    $links[] = [
                                        'keyword' => $word,
                                        'url' => "/services?search=" . urlencode($word), // ✅ FIXED
                                    ];
                                }
                            }

// fallback
                            if (empty($links)) {
                                $links = [
                                    [
                                        'keyword' => 'სერვისები',
                                        'url' => '/services', // ✅ FIXED
                                    ],
                                ];
                            }

                            $set('seo.internal_links', $links);
                        }),

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

                    View::make('filament.seo-preview')
                        ->reactive(),

                ])
                ->columnSpanFull(),

            /*
            | 🔥 SCHEMA TYPE (UNCHANGED)
            */
            Select::make('seo.schema_type')
                ->label('Schema Type')
                ->options([
                    'Service' => 'Service (Recommended)',
                    'LocalBusiness' => 'Local Business',
                    'WebPage' => 'WebPage',
                ])
                ->default('Service')
                ->reactive()

                ->afterStateUpdated(function ($state, callable $set, callable $get) {

                    $type = $state ?: 'Service';

                    $set('seo.schema', self::generateSchema(
                        $type,
                        $get('title'),
                        $get('long_description')
                    ));
                })

                ->afterStateHydrated(function ($state, callable $set, callable $get) {

                    if ($get('seo.schema')) return;

                    $type = $state ?: 'Service';

                    $set('seo.schema', self::generateSchema(
                        $type,
                        $get('title'),
                        $get('long_description')
                    ));
                }),

            Textarea::make('seo.schema')
                ->label('Schema JSON (JSON-LD)')
                ->rows(10)
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
        ]);
    }
    protected static function generateSchema($type, $title = null, $description = null): ?string
    {
        $baseUrl = SocialLinks::frontendUrl('/');
        $name = config('app.name');

        return match ($type) {

            'Service' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "Service",
                "name" => $title,
                "description" => $description,
                "provider" => [
                    "@type" => "Organization",
                    "name" => $name,
                    "url" => $baseUrl,
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            'LocalBusiness' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "LocalBusiness",
                "name" => $name,
                "description" => $description,
                "areaServed" => "Tbilisi",
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            default => json_encode([
                "@context" => "https://schema.org",
                "@type" => "WebPage",
                "name" => $title,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        };
    }
}
