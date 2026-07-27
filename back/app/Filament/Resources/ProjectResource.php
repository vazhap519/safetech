<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Support\AdminIconOptions;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Filament\Support\StableSlug;
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

    protected static ?string $navigationLabel = 'Projects';

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projects';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Projects;

    public static function form(Schema $schema): Schema
    {
        $detailCards = [
            Select::make('icon')
                ->label('Icon')
                ->options(AdminIconOptions::content())
                ->searchable()
                ->preload(),
            TextInput::make('title')->label('Title')->required(),
            Textarea::make('description')->label('Description')->required(),
            Toggle::make('featured')->label('Featured'),
        ];

        $valueLabel = [
            TextInput::make('value')->label('Value')->required(),
            TextInput::make('label')->label('Label')->required(),
        ];

        return $schema->components([
            Section::make('Main project information')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(StableSlug::syncOnCreate()),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Generated automatically from the name, but still editable.'),
                    TextInput::make('title')->label('Headline')->required(),
                    Textarea::make('description')->label('Description')->required(),
                    Textarea::make('seo_description')
                        ->label('SEO description')
                        ->required()
                        ->maxLength(320),
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label('Cover image')
                        ->collection('cover')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240)
                        ->imagePreviewHeight('150'),
                    TextInput::make('image_alt')
                        ->label('Image alt text')
                        ->requiredWith('cover'),
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('video_url')
                        ->label('YouTube URL')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder('https://www.youtube.com/watch?v=...'),
                    TextInput::make('technology')->label('Technology'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options(AdminIconOptions::content())
                        ->searchable()
                        ->preload()
                        ->default('business')
                        ->required(),
                    Select::make('accent')
                        ->label('Accent')
                        ->options([
                            'primary' => 'Primary',
                            'secondary' => 'Secondary',
                        ])
                        ->default('primary')
                        ->required(),
                ])
                ->columns(2),

            Section::make('Translations and SEO (KA/EN/RU)')
                ->description('The main fields above remain fallback content. These translations are consumed automatically on the frontend.')
                ->schema([
                    ...LocalizedContentFields::inputs('name', 'Project name'),
                    ...LocalizedContentFields::inputs('title', 'Headline'),
                    ...LocalizedContentFields::inputs('description', 'Description', textarea: true),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO title'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO description', textarea: true),
                    ...LocalizedContentFields::inputs('imageAlt', 'Image alt'),
                    ...LocalizedContentFields::inputs('technology', 'Technology'),
                    ...LocalizedContentFields::inputs('card.title', 'Card title'),
                    ...LocalizedContentFields::inputs('card.description', 'Card description', textarea: true),
                    ...LocalizedContentFields::inputs('featured.title', 'Featured title'),
                    ...LocalizedContentFields::inputs('featured.category', 'Featured category'),
                    ...LocalizedContentFields::inputs('featured.imageAlt', 'Featured image alt'),
                    LocalizedContentFields::customEntries('Examples: meta.0.label, spec.0.value, challenge.0.title, result.0.description'),
                ])
                ->columns(3),

            Section::make('Project sections')
                ->schema([
                    Repeater::make('meta')
                        ->label('Meta')
                        ->schema($valueLabel)
                        ->columns(2),
                    Repeater::make('scope')
                        ->label('Scope')
                        ->schema($valueLabel)
                        ->columns(2),
                    Repeater::make('specs')
                        ->label('Specifications')
                        ->schema($valueLabel)
                        ->columns(2),
                    Repeater::make('challenges')
                        ->label('Challenges')
                        ->schema($detailCards)
                        ->columns(4)
                        ->collapsible(),
                    Repeater::make('solutions')
                        ->label('Solutions')
                        ->schema($detailCards)
                        ->columns(4)
                        ->collapsible(),
                    Repeater::make('process')
                        ->label('Process')
                        ->schema([
                            TextInput::make('title')->label('Step')->required(),
                            Textarea::make('description')->label('Description')->required(),
                        ])
                        ->columns(2),
                    SpatieMediaLibraryFileUpload::make('media_gallery')
                        ->label('Gallery')
                        ->collection('gallery')
                        ->conversion('webp')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    Repeater::make('results')
                        ->label('Results')
                        ->schema([
                            TextInput::make('value')->label('Value')->required(),
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('description')->label('Description')->required(),
                            Select::make('accent')
                                ->label('Accent')
                                ->options([
                                    'primary' => 'Primary',
                                    'secondary' => 'Secondary',
                                ])
                                ->default('primary'),
                        ])
                        ->columns(4),
                    Repeater::make('related')
                        ->label('Related projects')
                        ->schema([
                            Select::make('slug')
                                ->label('Project')
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
                                ->label('Card title')
                                ->helperText('Optional override for the related project title.'),
                            TextInput::make('category')->label('Category'),
                            TextInput::make('imageAlt')->label('Image alt'),
                        ])
                        ->columns(2),
                ]),

            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_featured')->label('Featured'),
                    Toggle::make('is_published')->label('Published'),
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->default(0),
                    DateTimePicker::make('published_at')
                        ->label('Published at'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                TextColumn::make('published_at')->label('Published at')->dateTime()->sortable(),
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
