<?php

namespace App\Filament\Resources\SeoPages\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\View;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SeoPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('SEO (Google Optimization)')
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | PAGE SELECT
                    |--------------------------------------------------------------------------
                    */
                    Select::make('key')
                        ->label('Page')
                        ->options(function () {
                            return collect(Route::getRoutes())
                                ->filter(function ($route) {
                                    $uri = $route->uri();

                                    return
                                        in_array('GET', $route->methods()) &&
                                        str_starts_with($uri, 'api') &&
                                        !str_contains($uri, '{') &&
                                        !str_contains($uri, 'debug') &&
                                        !str_contains($uri, 'sanctum');
                                })
                                ->mapWithKeys(function ($route) {
                                    $uri = $route->uri();

                                    $key = preg_replace('#^api/?#', '', $uri);
                                    $key = $key ?: 'home';

                                    return [
                                        $key => Str::of($key)->replace('-', ' ')->title()
                                    ];
                                })
                                ->unique()
                                ->sortKeys()
                                ->toArray();
                        })
                        ->required()
                        ->reactive()
                        ->searchable()
                        ->afterStateUpdated(fn ($state, $set) =>
                        $set('slug', '/' . trim($state, '/'))
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | SLUG
                    |--------------------------------------------------------------------------
                    */
                    TextInput::make('slug')
                        ->label('URL')
                        ->disabled()
                        ->dehydrated(),

                    /*
                    |--------------------------------------------------------------------------
                    | TITLE
                    |--------------------------------------------------------------------------
                    */
                    TextInput::make('title')
                        ->label('SEO Title')
                        ->required()
                        ->live(),

                    /*
                    |--------------------------------------------------------------------------
                    | DESCRIPTION
                    |--------------------------------------------------------------------------
                    */
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->live(),

                    /*
                    |--------------------------------------------------------------------------
                    | KEYWORDS
                    |--------------------------------------------------------------------------
                    */
                    Repeater::make('keywords')
                        ->label('Keywords')
                        ->simple(
                            TextInput::make('value')
                                ->placeholder('keyword...')
                        )
                        ->formatStateUsing(fn ($state) =>
                        collect($state ?? [])
                            ->map(fn ($item) => is_array($item) ? $item : ['value' => $item])
                            ->toArray()
                        )
                        ->minItems(3)
                        ->maxItems(10),

                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE SEO
                    |--------------------------------------------------------------------------
                    */
                    Action::make('generate')
                        ->label('🤖 Generate SEO')
                        ->color('success')
                        ->action(function ($set, $get) {

                            $page = $get('key');
                            if (!$page) return;

                            $titleBase = Str::title(str_replace('-', ' ', $page));

                            // TITLE (40-60)
                            $title = "{$titleBase} თბილისში | კომპიუტერული სერვისები";

                            if (mb_strlen($title) < 40) {
                                $title .= " და ქსელების კონფიგურაცია";
                            }

                            $title = mb_substr($title, 0, 60);

                            $set('title', $title);

                            // DESCRIPTION (120-160)
                            $base = "{$titleBase} თბილისში";
                            $services = "კომპიუტერული სერვისები, ქსელების კონფიგურაცია, უსაფრთხოების სისტემები, როუტერებისა და სერვერების გამართვა";

                            $variants = [
                                "სწრაფი და პროფესიონალური მომსახურება.",
                                "საუკეთესო ხარისხი და გარანტია.",
                                "გამოცდილი სპეციალისტების გუნდი.",
                            ];

                            $cta = $variants[array_rand($variants)];

                            $description = "{$base} ✔ {$services} ✔ {$cta}";

                            while (mb_strlen($description) < 120) {
                                $description .= " მაღალი ხარისხი.";
                            }

                            $description = mb_substr($description, 0, 155);

                            $set('description', $description);

                            // KEYWORDS
                            $set('keywords', collect([
                                $titleBase,
                                "{$titleBase} თბილისი",
                                "{$titleBase} სერვისი",
                                "IT სერვისი",
                                "ქსელები",
                                "როუტერი",
                                "უსაფრთხოების სისტემები",
                            ])->map(fn ($k) => ['value' => $k])->toArray());
                        }),

                    /*
                    |--------------------------------------------------------------------------
                    | SEO SCORE
                    |--------------------------------------------------------------------------
                    */
                    Placeholder::make('seo_score')
                        ->label('SEO Score')
                        ->content(function ($get) {

                            $score = 0;

                            $titleLength = mb_strlen($get('title') ?? '');
                            $descLength = mb_strlen($get('description') ?? '');
                            $keywordsCount = count($get('keywords') ?? []);

                            if ($titleLength >= 40 && $titleLength <= 60) $score += 30;
                            if ($descLength >= 120 && $descLength <= 160) $score += 30;
                            if ($keywordsCount >= 3) $score += 20;
                            if (!empty($get('slug'))) $score += 20;

                            return "Score: {$score}/100";
                        }),

                    /*
                    |--------------------------------------------------------------------------
                    | PREVIEW
                    |--------------------------------------------------------------------------
                    */
                    View::make('filament.seo-preview')
                        ->reactive(),

                ])
                ->columnSpanFull(),

        ]);
    }
}
