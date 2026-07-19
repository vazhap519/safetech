<?php

namespace App\Filament\Resources\SeoPages\Schemas;

use App\Support\SiteSettings;
use App\Support\SocialLinks;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\View;

use Illuminate\Support\Str;

class SeoPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('SEO (Google Optimization)')
                ->schema([

                    /*
                    |--------------------------------------------------
                    | PAGE
                    |--------------------------------------------------
                    */
                    Select::make('key')
                        ->label('გვერდი')
                        ->options([
                            'home' => 'მთავარი',
                            'about' => 'ჩვენ შესახებ',
                            'services' => 'სერვისები',
                            'service-calculator' => 'სერვისების კალკულატორი',
                            'projects' => 'პროექტები',
                            'blog' => 'ბლოგი',
                            'contact' => 'კონტაქტი',
                            'privacy' => 'კონფიდენციალურობა',
                        ])
                        ->required()
                        ->reactive()
                        ->searchable()
                        ->afterStateUpdated(fn ($state, $set) => $set(
                            'slug',
                            $state === 'home' ? '/' : '/'.trim((string) $state, '/'),
                        )),

                    /*
                    |--------------------------------------------------
                    | SLUG
                    |--------------------------------------------------
                    */
                    TextInput::make('slug')
                        ->label('URL')
                        ->disabled()
                        ->dehydrated(),

                    /*
                    |--------------------------------------------------
                    | TITLE
                    |--------------------------------------------------
                    */
                    TextInput::make('title')
                        ->label('SEO სათაური (ქართული fallback)')
                        ->required()
                        ->live(),

                    /*
                    |--------------------------------------------------
                    | DESCRIPTION
                    |--------------------------------------------------
                    */
                    Textarea::make('description')
                        ->label('Meta აღწერა (ქართული fallback)')
                        ->rows(3)
                        ->live(),

                    /*
                    |--------------------------------------------------
                    | KEYWORDS
                    |--------------------------------------------------
                    */
                    Repeater::make('keywords')
                        ->label('საკვანძო სიტყვები (ქართული fallback)')
                        ->schema([
                            TextInput::make('value')->required(),
                        ])
                        ->formatStateUsing(fn ($state) =>
                        collect($state ?? [])
                            ->map(fn ($i) => is_array($i) ? $i : ['value' => $i])
                            ->toArray()
                        )
                        ->dehydrateStateUsing(fn ($state) =>
                        collect($state ?? [])
                            ->map(fn ($i) => is_array($i) ? $i : ['value' => $i])
                            ->toArray()
                        )
                        ->minItems(3)
                        ->maxItems(10),

                    /*
                    |--------------------------------------------------
                    | AUTO SEO
                    |--------------------------------------------------
                    */
                    Action::make('generate')
                        ->label('SEO ტექსტის შექმნა')
                        ->color('success')
                        ->action(function ($set, $get) {

                            $page = $get('key');
                            if (!$page) return;

                            $base = Str::title(str_replace('-', ' ', $page));

                            $set('title', mb_substr("{$base} თბილისში | კომპიუტერული სერვისები", 0, 60));

                            $set('description', mb_substr(
                                "{$base} თბილისში ✔ კომპიუტერული სერვისები ✔ ქსელები ✔ უსაფრთხოების სისტემები ✔ პროფესიონალური მომსახურება.",
                                0,
                                155
                            ));

                            $set('keywords', collect([
                                $base,
                                "{$base} თბილისი",
                                "IT სერვისი",
                                "ქსელები",
                                "უსაფრთხოების სისტემები",
                            ])->map(fn ($k) => ['value' => $k])->toArray());
                        }),

                    /*
                    |--------------------------------------------------
                    | SCORE
                    |--------------------------------------------------
                    */
                    Placeholder::make('seo_score')
                        ->content(function ($get) {

                            $score = 0;

                            if (mb_strlen($get('title') ?? '') >= 40) $score += 30;
                            if (mb_strlen($get('description') ?? '') >= 120) $score += 30;
                            if (count($get('keywords') ?? []) >= 3) $score += 20;
                            if ($get('slug')) $score += 20;

                            return "Score: {$score}/100";
                        }),

                    /*
                    |--------------------------------------------------
                    | SCHEMA TYPE
                    |--------------------------------------------------
                    */
                    Select::make('schema_type')
                        ->label('Schema ტიპი')
                        ->options([
                            'WebPage' => 'WebPage',
                            'WebSite' => 'WebSite',
                            'Article' => 'Article',
                            'LocalBusiness' => 'LocalBusiness',
                            'Service' => 'Service',
                        ])
                        ->default('WebPage')
                        ->reactive()

                        ->afterStateUpdated(function ($state, $set, $get) {

                            // ❗ overwrite არ მოხდეს
                            if ($get('schema')) return;

                            $set('schema', self::generateSchema($state));
                        })

                        ->afterStateHydrated(function ($state, $set, $get) {

                            if ($get('schema')) return;

                            $set('schema', self::generateSchema($state));
                        }),

                    /*
                    |--------------------------------------------------
                    | SCHEMA JSON (VISIBLE FIX)
                    |--------------------------------------------------
                    */
                    Textarea::make('schema')
                        ->label('Schema JSON')
                        ->rows(10)
                        ->helperText('Auto-generated. You can edit.')
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
                        ->rules([
                            fn () => function ($attr, $value, $fail) {
                                if (!$value) return;
                                json_decode($value);
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    $fail('Invalid JSON');
                                }
                            }
                        ]),

                    Toggle::make('noindex')
                        ->label('საძიებო სისტემებში არ გამოჩნდეს')
                        ->default(false),

                    SpatieMediaLibraryFileUpload::make('og_image')
                        ->label('Google / Open Graph სურათი (1200x630)')
                        ->collection('og_image')
                        ->image()
                        ->imageEditor(),

                    SpatieMediaLibraryFileUpload::make('share_image')
                        ->label('Social share სურათი (1200x630)')
                        ->collection('share_image')
                        ->image()
                        ->imageEditor(),

                    /*
                    |--------------------------------------------------
                    | PREVIEW
                    |--------------------------------------------------
                    */
                    View::make('filament.seo-preview')->reactive(),

                ]),

            Section::make('SEO ტექსტები 3 ენაზე')
                ->description('თითოეულ ენას აქვს დამოუკიდებელი title, description, Open Graph ტექსტი და keywords. ცარიელი ველი ქართულ fallback-ს გამოიყენებს.')
                ->schema([
                    ...self::translationInputs('title', 'SEO სათაური'),
                    ...self::translationInputs('description', 'Meta აღწერა', true),
                    ...self::translationInputs('og_title', 'Open Graph სათაური'),
                    ...self::translationInputs('og_description', 'Open Graph აღწერა', true),
                    TagsInput::make('translations.keywords.ka')->label('Keywords (ქართული)'),
                    TagsInput::make('translations.keywords.en')->label('Keywords (English)'),
                    TagsInput::make('translations.keywords.ru')->label('Keywords (Русский)'),
                ])
                ->columns(3),
        ]);
    }

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

    /*
    |--------------------------------------------------
    | SCHEMA GENERATOR
    |--------------------------------------------------
    */
    protected static function generateSchema($type): ?string
    {
        $baseUrl = SocialLinks::frontendUrl('/');
        $name = config('app.name');

        // ✅ სწორ ადგილას
        $settings = SiteSettings::businessProfile();

        return match ($type) {

            /*
            |--------------------------------------------------
            | 🏠 WEBSITE
            |--------------------------------------------------
            */
            'WebSite' => json_encode([
                [
                    "@context" => "https://schema.org",
                    "@type" => "Organization",
                    "name" => $name,
                    "url" => $baseUrl,
                    "logo" => $baseUrl . "/logo.png",
                    "sameAs" => array_filter([
                        $settings?->facebook,
                        $settings?->linkedin,
                        $settings?->instagram,
                    ])
                ],
                [
                    "@type" => "WebSite",
                    "name" => $name,
                    "url" => $baseUrl,
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => $baseUrl . "/search?q={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            /*
            |--------------------------------------------------
            | 📰 ARTICLE
            |--------------------------------------------------
            */
            'Article' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "Article",
                "headline" => "Title",
                "description" => "Description",
                "author" => [
                    "@type" => "Organization",
                    "name" => $name
                ],
                "publisher" => [
                    "@type" => "Organization",
                    "name" => $name,
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $baseUrl . "/logo.png"
                    ]
                ],
                "mainEntityOfPage" => $baseUrl,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            /*
            |--------------------------------------------------
            | 🏢 LOCAL BUSINESS (🔥 FULL FIX)
            |--------------------------------------------------
            */
            'LocalBusiness' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "LocalBusiness",

                "name" => $name,
                "url" => $baseUrl,

                // ✅ dynamic
                "telephone" => $settings?->phone,
                "email" => $settings?->email,

                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => $settings?->address,
                    "addressLocality" => $settings?->city,
                    "addressCountry" => $settings?->country ?? 'GE',
                ],

                "geo" => [
                    "@type" => "GeoCoordinates",
                    "latitude" => $settings?->lat,
                    "longitude" => $settings?->lng,
                ],

                "openingHoursSpecification" => [
                    [
                        "@type" => "OpeningHoursSpecification",
                        "dayOfWeek" => [
                            "Monday","Tuesday","Wednesday","Thursday","Friday"
                        ],
                        "opens" => $settings?->open_time ?? "09:00",
                        "closes" => $settings?->close_time ?? "18:00"
                    ]
                ],

                "sameAs" => array_filter([
                    $settings?->facebook,
                    $settings?->instagram,
                    $settings?->linkedin,
                ]),

                "areaServed" => $settings?->country ?? 'Georgia'

            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            /*
            |--------------------------------------------------
            | ⚙️ SERVICE
            |--------------------------------------------------
            */
            'Service' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "Service",
                "name" => "Service Name",
                "provider" => [
                    "@type" => "Organization",
                    "name" => $name,
                    "url" => $baseUrl
                ],
                "areaServed" => $settings?->country ?? "Georgia",
                "description" => "Service description"
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            /*
            |--------------------------------------------------
            | 📄 DEFAULT
            |--------------------------------------------------
            */
            'WebPage' => json_encode([
                "@context" => "https://schema.org",
                "@type" => "WebPage",
                "name" => $name,
                "url" => $baseUrl
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

            default => null,
        };
    }
}
