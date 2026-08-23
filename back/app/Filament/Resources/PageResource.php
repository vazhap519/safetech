<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Filament\Support\StableSlug;
use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationLabel = 'გვერდები';

    protected static ?string $modelLabel = 'გვერდი';

    protected static ?string $pluralModelLabel = 'გვერდები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Pages;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page')
                ->schema([
                    TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(StableSlug::syncOnCreate()),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->helperText('Public URL is /pages/{slug}. Reserved legal slugs privacy and terms are exposed at /privacy and /terms.'),
                    Textarea::make('excerpt')->rows(3),
                    Textarea::make('content')->required()->rows(14)->helperText('Plain paragraphs are supported. Legal pages also support section headings beginning with ## and bullet lists beginning with -.'),
                    SpatieMediaLibraryFileUpload::make('cover')->label('Cover image')->collection('cover')->conversion('webp')->image()->imageEditor()->maxSize(10240),
                ])->columns(2),
            Section::make('Translations (KA / EN / RU)')
                ->schema([
                    ...LocalizedContentFields::inputs('title', 'Title'),
                    ...LocalizedContentFields::inputs('excerpt', 'Excerpt', textarea: true),
                    ...LocalizedContentFields::inputs('content', 'Content', textarea: true, rows: 10),
                    ...LocalizedContentFields::inputs('seoTitle', 'SEO title'),
                    ...LocalizedContentFields::inputs('seoDescription', 'SEO description', textarea: true),
                ])->columns(3),
            Section::make('Search and publishing')
                ->schema([
                    TextInput::make('seo_title')->label('SEO title')->maxLength(255),
                    Textarea::make('seo_description')->label('SEO description')->rows(3)->maxLength(320),
                    TagsInput::make('keywords')->label('Keywords'),
                    Toggle::make('is_published')->label('Published')->default(false),
                    Toggle::make('noindex')->label('Exclude from search and sitemap')->default(false),
                    DateTimePicker::make('published_at')->label('Published at'),
                    TextInput::make('sort_order')->numeric()->default(0),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
                IconColumn::make('noindex')->label('Noindex')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('წაშლა')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('არჩეული გვერდების წაშლა')
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
