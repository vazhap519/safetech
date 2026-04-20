<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /* =========================
               🟢 BASIC
            ========================= */
            Section::make('Basic')
                ->schema([

                    TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($set, $state) =>
                        $set('slug', Str::slug($state))
                        ),

                    TextInput::make('slug')->required(),

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()

                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($set, $state) =>
                                $set('slug', Str::slug($state))
                                ),
                            TextInput::make('slug')->required(),
                        ])

                        ->editOptionForm([
                            TextInput::make('name')->required(),
                            TextInput::make('slug')->required(),
                        ]),

                    Toggle::make('is_published')->default(true),

                    Textarea::make('excerpt')->rows(3),
                    Textarea::make('content')->rows(6),

                    TextInput::make('video_url')
                        ->label('YouTube / Vimeo'),

                ])
                ->columns(2),

            /* =========================
               📸 MEDIA
            ========================= */
            Section::make('Media')
                ->schema([

                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->image()
                        ->imagePreviewHeight('150'),

                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->collection('gallery')
                        ->multiple()
                        ->reorderable()
                        ->image(),

                ]),

            /* =========================
               🔍 SEO
            ========================= */
            Section::make('SEO (Google Optimization)')
                ->schema([

                    TextInput::make('seo.title')
                        ->label('SEO Title')
                        ->maxLength(255),

                    Toggle::make('seo.noindex')->default(false),

                    Textarea::make('seo.description')->rows(3),

                    Repeater::make('seo.keywords')
                        ->simple(
                            TextInput::make('value')
                        ),

                    Repeater::make('seo.content')
                        ->schema([
                            Textarea::make('text')->rows(3),
                        ])
                        ->columnSpanFull(),

                    Action::make('generate_seo')
                        ->label('🤖 Generate SEO')
                        ->color('success')
                        ->action(function ($set, $get) {

                            $title = $get('title');
                            $content = $get('content');

                            if (!$title) return;

                            $description = $content
                                ? mb_substr(strip_tags($content), 0, 160)
                                : $title;

                            $set('seo.title', $title . ' | Safetech');
                            $set('seo.description', $description);

                            $set('seo.keywords', [
                                ['value' => $title],
                                ['value' => 'IT პროექტები'],
                            ]);

                            $set('seo.content', [
                                ['text' => $description],
                            ]);

                            $set('seo.schema', self::generateSchema(
                                $get('seo.schema_type') ?? 'project',
                                $title,
                                $description
                            ));
                        }),

                    Placeholder::make('seo_score')
                        ->content(function ($get) {

                            $score = 0;

                            if (strlen($get('seo.title')) >= 40) $score += 25;
                            if (strlen($get('seo.description')) >= 120) $score += 25;
                            if (!empty($get('seo.keywords'))) $score += 20;
                            if (!empty($get('seo.content'))) $score += 20;
                            if (!empty($get('seo.schema'))) $score += 10;

                            return "SEO Score: {$score}/100";
                        }),

                    View::make('filament.seo-preview'),

                    Select::make('seo.schema_type')
                        ->options([
                            'project' => 'Project',
                            'Service' => 'Service',
                            'LocalBusiness' => 'Local Business',
                            'WebPage' => 'WebPage',
                        ])
                        ->default('project')
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $set('seo.schema', self::generateSchema(
                                $state,
                                $get('title'),
                                $get('content')
                            ));
                        }),

                    Textarea::make('seo.schema')
                        ->rows(12)
                        ->dehydrateStateUsing(function ($state) {
                            if (!$state) {
                                return null;
                            }

                            $decoded = json_decode($state, true);

                            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                        })
                        ->formatStateUsing(fn ($state) =>
                        is_array($state)
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                            : $state
                        ),

                ])
                ->columnSpanFull(),

        ]);
    }

    protected static function generateSchema($type, $title = null, $description = null): ?string
    {
        return json_encode([
            "@context" => "https://schema.org",
            "@type" => match ($type) {
                'project' => 'CreativeWork',
                'Service' => 'Service',
                'LocalBusiness' => 'LocalBusiness',
                default => 'WebPage',
            },
            "name" => $title,
            "description" => $description,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
