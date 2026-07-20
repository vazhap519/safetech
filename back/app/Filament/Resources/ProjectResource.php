<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

    protected static ?string $navigationLabel = 'პროექტები';

    protected static ?string $modelLabel = 'პროექტი';

    protected static ?string $pluralModelLabel = 'პროექტები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Projects;

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
                ->label('URL კოდი')
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
                ->conversion('webp')
                ->image()
                ->imageEditor()
                ->maxSize(10240)
                ->imagePreviewHeight('150'),
            TextInput::make('image_alt')
                ->label('ფოტოს ALT ტექსტი')
                ->requiredWith('cover'),
            Select::make('category_id')
                ->label('კატეგორია')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('video_url')
                ->label('YouTube ვიდეოს URL')
                ->url()
                ->maxLength(2048)
                ->placeholder('https://www.youtube.com/watch?v=...')
                ->helperText('ჩასვით YouTube-ის ბმული; ვიდეო გამოჩნდება პროექტის გვერდზე.'),
            TextInput::make('technology')
                ->label('ტექნოლოგია'),
            Section::make('კონტენტი და SEO 3 ენაზე')
                ->description('ძირითადი ველები რჩება ქართულ fallback-ად. აქ შეიყვანეთ KA/EN/RU ტექსტები, რომლებიც ფრონტზე ავტომატურად წავა project.{slug} key-ებით.')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'პროექტის სახელი'),
                    ...LocalizedContentFields::inputs('title', 'სათაური'),
                    ...LocalizedContentFields::inputs('description', 'აღწერა', textarea: true),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO სათაური'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO აღწერა', textarea: true),
                    ...LocalizedContentFields::inputs('imageAlt', 'ფოტოს ALT'),
                    ...LocalizedContentFields::inputs('technology', 'ტექნოლოგია'),
                    ...LocalizedContentFields::inputs('card.title', 'ბარათის სათაური'),
                    ...LocalizedContentFields::inputs('card.description', 'ბარათის აღწერა', textarea: true),
                    ...LocalizedContentFields::inputs('featured.title', 'გამორჩეულის სათაური'),
                    ...LocalizedContentFields::inputs('featured.category', 'გამორჩეულის კატეგორია'),
                    ...LocalizedContentFields::inputs('featured.imageAlt', 'გამორჩეულის ALT'),
                    LocalizedContentFields::customEntries('მაგ: meta.0.label, spec.0.value, challenge.0.title, result.0.description'),
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
                ->label('მედია გალერეა')
                ->collection('gallery')
                ->conversion('webp')
                ->multiple()
                ->reorderable()
                ->image()
                ->imageEditor()
                ->maxSize(10240),
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
                TextColumn::make('slug')->label('URL კოდი')->searchable(),
                TextColumn::make('category.name')->label('კატეგორია')->sortable(),
                IconColumn::make('is_featured')
                    ->label('გამორჩეული')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('გამოქვეყნებული')
                    ->boolean(),
                TextColumn::make('published_at')->label('გამოქვეყნების თარიღი')->dateTime()->sortable(),
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
