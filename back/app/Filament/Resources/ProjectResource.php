<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static bool $isDiscovered = false;

    protected static ?string $navigationLabel = 'პროექტები';

    protected static ?string $modelLabel = 'პროექტი';

    protected static ?string $pluralModelLabel = 'პროექტები';

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

    private static function customTranslationEntries(): Repeater
    {
        return Repeater::make('translations.entries')
            ->label('დამატებითი თარგმნის key-ები')
            ->schema([
                TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->helperText('მაგ: meta.0.label, spec.0.value, challenge.0.title, result.0.description'),
                TextInput::make('ka')->label('ქართული'),
                TextInput::make('en')->label('English'),
                TextInput::make('ru')->label('Русский'),
            ])
            ->columns(2)
            ->default([])
            ->collapsible()
            ->reorderable();
    }

    public static function form(Schema $schema): Schema
    {
        $detailCards = [
            TextInput::make('icon')->label('აიკონი'),
            TextInput::make('title')->label('სათაური')->required(),
            Textarea::make('description')->label('აღწერა')->required(),
            Toggle::make('featured')->label('გამორჩეული'),
        ];

        $valueLabel = [
            TextInput::make('value')->label('მნიშვნელობა')->required(),
            TextInput::make('label')->label('ეტიკეტი')->required(),
        ];

        return $schema->components([
            TextInput::make('name')
                ->label('სახელი')
                ->required(),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('გამოიყენება URL-ში და უნდა იყოს უნიკალური.'),
            TextInput::make('title')
                ->label('სათაური')
                ->required(),
            Textarea::make('description')
                ->label('აღწერა')
                ->required(),
            Textarea::make('seo_description')
                ->label('SEO აღწერა')
                ->required()
                ->maxLength(320),
            SpatieMediaLibraryFileUpload::make('cover')
                ->label('მთავარი ფოტო')
                ->collection('cover')
                ->image()
                ->imagePreviewHeight('150'),
            TextInput::make('image_alt')
                ->label('ფოტოს ALT ტექსტი')
                ->requiredWith('cover'),
            Select::make('category')
                ->label('კატეგორია')
                ->options([
                    'offices' => 'ოფისები',
                    'hotels' => 'სასტუმროები',
                    'warehouses' => 'საწყობები',
                    'factories' => 'საწარმოები',
                ])
                ->default('offices')
                ->required(),
            TextInput::make('video_url')
                ->label('YouTube video URL')
                ->url()
                ->maxLength(2048)
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->helperText('Paste a YouTube link here. The frontend will show it as a video on projects and project details.'),
            TextInput::make('technology')
                ->label('ტექნოლოგია'),
            Section::make('კონტენტი და SEO 3 ენაზე')
                ->description('ძირითადი ველები რჩება ქართულ fallback-ად. აქ შეიყვანეთ KA/EN/RU ტექსტები, რომლებიც ფრონტზე ავტომატურად წავა project.{slug} key-ებით.')
                ->schema([
                    ...self::translationInputs('name', 'პროექტის სახელი'),
                    ...self::translationInputs('title', 'სათაური'),
                    ...self::translationInputs('description', 'აღწერა', true),
                    ...self::translationInputs('seoDescription', 'SEO აღწერა', true),
                    ...self::translationInputs('imageAlt', 'ფოტოს ALT'),
                    ...self::translationInputs('technology', 'ტექნოლოგია'),
                    ...self::translationInputs('card.title', 'ბარათის სათაური'),
                    ...self::translationInputs('card.description', 'ბარათის აღწერა', true),
                    ...self::translationInputs('featured.title', 'Featured სათაური'),
                    ...self::translationInputs('featured.category', 'Featured კატეგორია'),
                    ...self::translationInputs('featured.imageAlt', 'Featured ALT'),
                    self::customTranslationEntries(),
                ])
                ->columns(3)
                ->columnSpanFull(),
            TextInput::make('icon')
                ->label('აიკონი')
                ->default('business')
                ->required()
                ->helperText('Material Symbol-ის სახელი.'),
            Select::make('accent')
                ->label('აქცენტი')
                ->options([
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                ])
                ->default('primary')
                ->required(),
            Repeater::make('meta')
                ->label('მეტა ინფორმაცია')
                ->schema($valueLabel)
                ->columns(2),
            Repeater::make('scope')
                ->label('მასშტაბი')
                ->schema($valueLabel)
                ->columns(2),
            Repeater::make('specs')
                ->label('სპეციფიკაციები')
                ->schema($valueLabel)
                ->columns(2),
            Repeater::make('challenges')
                ->label('გამოწვევები')
                ->schema($detailCards)
                ->columns(4)
                ->collapsible(),
            Repeater::make('solutions')
                ->label('გადაწყვეტილებები')
                ->schema($detailCards)
                ->columns(4)
                ->collapsible(),
            Repeater::make('process')
                ->label('პროცესი')
                ->schema([
                    TextInput::make('title')->label('ეტაპი')->required(),
                    Textarea::make('description')->label('აღწერა')->required(),
                ])
                ->columns(2),
            SpatieMediaLibraryFileUpload::make('media_gallery')
                ->label('Media gallery')
                ->collection('gallery')
                ->multiple()
                ->reorderable()
                ->image(),
            Repeater::make('gallery')
                ->label('გალერეა')
                ->schema([
                    FileUpload::make('src')
                        ->label('სურათი')
                        ->image()
                        ->disk('public')
                        ->directory('projects/gallery')
                        ->required(),
                    TextInput::make('alt')
                        ->label('ALT ტექსტი')
                        ->required(),
                ])
                ->columns(2),
            Repeater::make('results')
                ->label('შედეგები')
                ->schema([
                    TextInput::make('value')->label('მნიშვნელობა')->required(),
                    TextInput::make('title')->label('სათაური')->required(),
                    Textarea::make('description')->label('აღწერა')->required(),
                    Select::make('accent')
                        ->label('აქცენტი')
                        ->options([
                            'primary' => 'Primary',
                            'secondary' => 'Secondary',
                        ])
                        ->default('primary'),
                ])
                ->columns(4),
            Repeater::make('testimonial')
                ->label('კლიენტის შეფასება')
                ->maxItems(1)
                ->schema([
                    Textarea::make('quote')->label('ციტატა')->required(),
                    TextInput::make('author')->label('ავტორი')->required(),
                    TextInput::make('role')->label('როლი'),
                ])
                ->columns(3),
            Repeater::make('related')
                ->label('მსგავსი პროექტები')
                ->schema([
                    Select::make('slug')
                        ->label('დაკავშირებული პროექტი')
                        ->options(function (?Project $record): array {
                            return Project::query()
                                ->when(
                                    $record,
                                    fn ($query) => $query->whereKeyNot($record->getKey()),
                                )
                                ->orderBy('name')
                                ->pluck('name', 'slug')
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('title')
                        ->label('ბარათის სათაური')
                        ->helperText('შეგიძლიათ დატოვოთ იმავე სახელით ან შეცვალოთ მოკლე სათაურად.'),
                    TextInput::make('category')->label('კატეგორია'),
                    FileUpload::make('image')
                        ->label('სურათი')
                        ->image()
                        ->disk('public')
                        ->directory('projects'),
                    TextInput::make('imageAlt')->label('ALT ტექსტი'),
                ])
                ->columns(2),
            Toggle::make('is_featured')->label('გამორჩეული'),
            Toggle::make('is_published')->label('გამოქვეყნებულია'),
            TextInput::make('sort_order')
                ->label('რიგითობა')
                ->numeric()
                ->default(0),
            DateTimePicker::make('published_at')
                ->label('გამოქვეყნების თარიღი'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('სახელი')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')->searchable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
