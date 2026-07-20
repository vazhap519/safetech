<?php

namespace App\Filament\Resources\SeoPages\Schemas;

use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\StructuredDataJsonField;
use App\Support\Seo\SeoAudit;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class SeoPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ტექნიკური SEO')
                ->description('კანონიკური მისამართი, ინდექსაციის წესები, სოციალური preview და სტრუქტურირებული მონაცემები იმართება ერთი ადგილიდან.')
                ->schema([
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
                        ->unique(ignoreRecord: true)
                        ->live()
                        ->searchable()
                        ->afterStateUpdated(function ($state, Set $set): void {
                            $key = (string) $state;
                            $set('slug', $key === 'home' ? '/' : '/'.trim($key, '/'));
                            $set('schema_type', SeoAudit::recommendedSchemaType($key));
                        }),
                    TextInput::make('slug')
                        ->label('Canonical URL-ის გზა')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    TextInput::make('title')
                        ->label('SEO სათაური (ქართული სათადარიგო ტექსტი)')
                        ->required()
                        ->maxLength(180)
                        ->live(onBlur: true),
                    Textarea::make('description')
                        ->label('Meta აღწერა (ქართული სათადარიგო ტექსტი)')
                        ->required()
                        ->rows(3)
                        ->maxLength(500)
                        ->live(onBlur: true),
                    TagsInput::make('keywords')
                        ->label('კონტენტის საკვანძო თემები')
                        ->helperText('Google meta keywords-ს რეიტინგისთვის არ იყენებს; ეს სია მხოლოდ სარედაქციო დაგეგმვისთვისაა.')
                        ->formatStateUsing(fn ($state): array => collect($state ?? [])
                            ->map(fn ($keyword) => is_array($keyword) ? ($keyword['value'] ?? null) : $keyword)
                            ->filter()
                            ->values()
                            ->all())
                        ->dehydrateStateUsing(fn ($state): array => collect($state ?? [])
                            ->map(fn ($keyword): array => ['value' => trim((string) $keyword)])
                            ->filter(fn (array $keyword): bool => $keyword['value'] !== '')
                            ->values()
                            ->all()),
                    Select::make('schema_type')
                        ->label('Schema.org ტიპი')
                        ->options([
                            'WebPage' => 'WebPage',
                            'WebSite' => 'WebSite',
                            'AboutPage' => 'AboutPage',
                            'CollectionPage' => 'CollectionPage',
                            'WebApplication' => 'WebApplication',
                            'Blog' => 'Blog',
                            'ContactPage' => 'ContactPage',
                            'Article' => 'Article',
                            'LocalBusiness' => 'LocalBusiness',
                            'Service' => 'Service',
                        ])
                        ->required()
                        ->default('WebPage')
                        ->live(),
                    StructuredDataJsonField::make(
                        'ცარიელი დატოვეთ ავტომატური, მონაცემებზე მიბმული schema-სთვის. შეავსეთ მხოლოდ სპეციალური ჩანაცვლებისას.',
                    ),
                    Toggle::make('noindex')
                        ->label('საძიებო სისტემებში არ გამოჩნდეს')
                        ->helperText('ჩართეთ მხოლოდ staging, დროებითი ან განზრახ დახურული გვერდისთვის.')
                        ->default(false),
                    SpatieMediaLibraryFileUpload::make('og_image')
                        ->label('Google / Open Graph სურათი')
                        ->helperText('რეკომენდებული ფორმატი 1200x630; ატვირთვისას იქმნება WebP ვერსია.')
                        ->collection('og_image')
                        ->conversion('og')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    SpatieMediaLibraryFileUpload::make('share_image')
                        ->label('სოციალურ ქსელში გასაზიარებელი სურათი')
                        ->helperText('თუ ცარიელია, გამოიყენება Open Graph სურათი.')
                        ->collection('share_image')
                        ->conversion('og')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    Action::make('normalizeSeo')
                        ->label('ტექნიკური ხარვეზების გასწორება')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('success')
                        ->action(function (Get $get, Set $set): void {
                            $fixed = SeoAudit::normalize(self::auditState($get));

                            foreach (['slug', 'title', 'description', 'keywords', 'schema_type', 'schema'] as $field) {
                                $set($field, $fixed[$field] ?? null);
                            }

                            foreach (SeoAudit::LOCALES as $locale) {
                                foreach (['title', 'description', 'og_title', 'og_description'] as $field) {
                                    $set(
                                        "translations.fields.{$field}.{$locale}",
                                        data_get($fixed, "translations.fields.{$field}.{$locale}"),
                                    );
                                }
                            }

                            Notification::make()
                                ->title('ტექნიკური SEO გასწორდა')
                                ->body('ტექსტი არ ითარგმნა და არ გადაიწერა; დარჩენილი სარედაქციო საკითხები ქულაში ჩანს.')
                                ->success()
                                ->send();
                        }),
                    Placeholder::make('seo_audit')
                        ->label('გაფართოებული SEO შემოწმება')
                        ->content(fn (Get $get): HtmlString => self::auditHtml(SeoAudit::analyze(self::auditState($get))))
                        ->columnSpanFull(),
                    View::make('filament.seo-preview')
                        ->reactive()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('SEO ტექსტები 3 ენაზე')
                ->description('თითოეულ ენას აქვს დამოუკიდებელი სათაური, აღწერა და Open Graph preview. ქართული ცარიელი ველი სათადარიგო ტექსტს გამოიყენებს.')
                ->schema([
                    ...LocalizedContentFields::inputs('title', 'SEO სათაური', maxLength: 180, live: true),
                    ...LocalizedContentFields::inputs('description', 'Meta აღწერა', textarea: true, maxLength: 500, live: true),
                    ...LocalizedContentFields::inputs('og_title', 'Open Graph სათაური', maxLength: 180, live: true),
                    ...LocalizedContentFields::inputs('og_description', 'Open Graph აღწერა', textarea: true, maxLength: 500, live: true),
                    TagsInput::make('translations.keywords.ka')->label('კონტენტის თემები (ქართული)'),
                    TagsInput::make('translations.keywords.en')->label('კონტენტის თემები (ინგლისური)'),
                    TagsInput::make('translations.keywords.ru')->label('Темы контента (Русский)'),
                ])
                ->columns(3),
        ]);
    }

    /** @return array<string, mixed> */
    private static function auditState(Get $get): array
    {
        return [
            'key' => $get('key'),
            'slug' => $get('slug'),
            'title' => $get('title'),
            'description' => $get('description'),
            'keywords' => $get('keywords') ?? [],
            'schema_type' => $get('schema_type'),
            'schema' => $get('schema'),
            'noindex' => $get('noindex'),
            'translations' => $get('translations') ?? [],
        ];
    }

    /** @param array{score: int, issues: array<int, string>, notes: array<int, string>} $audit */
    private static function auditHtml(array $audit): HtmlString
    {
        $issues = $audit['issues']
            ? '<ul style="margin-top:.5rem;list-style:disc;padding-left:1.25rem">'.collect($audit['issues'])
                ->map(fn (string $issue): string => '<li>'.e($issue).'</li>')
                ->implode('').'</ul>'
            : '<p style="margin-top:.5rem">ძირითადი ტექნიკური და სარედაქციო შემოწმებები გავლილია.</p>';

        $notes = '<ul style="margin-top:.5rem;color:#6b7280;list-style:disc;padding-left:1.25rem">'.collect($audit['notes'])
            ->map(fn (string $note): string => '<li>'.e($note).'</li>')
            ->implode('').'</ul>';

        return new HtmlString(
            '<div><strong>შიდა SEO QA: '.e((string) $audit['score']).'/100</strong>'.$issues.$notes.'</div>',
        );
    }
}
