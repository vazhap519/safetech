<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageImageResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\SiteSetting;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageImageResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationLabel = 'გვერდების სურათები';

    protected static ?string $modelLabel = 'გვერდების სურათები';

    protected static ?string $pluralModelLabel = 'გვერდების სურათები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Pages;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('მთავარი გვერდი')
                ->description('მთავარი გვერდის დამოუკიდებელი ვიზუალური მასალა. რეკომენდებულია ფართო, მაღალი ხარისხის ფოტო ტექსტის გარეშე.')
                ->schema([
                    self::imageUpload('page_home_hero', 'home_hero', 'Hero სურათი', 'გამოჩნდება მთავარი გვერდის პირველ ეკრანზე.'),
                    self::imageUpload('page_home_infrastructure', 'home_infrastructure', 'ინფრასტრუქტურის ბლოკის სურათი', 'გამოჩნდება ინფრასტრუქტურის/ქსელური სისტემების ბლოკში.'),
                ])
                ->columns(2),

            Section::make('სერვისები და პროექტები')
                ->description('ეს ფოტოები გამოიყენება მხოლოდ შესაბამისი ჩამონათვალის გვერდის Hero ბლოკში და არ ცვლის კონკრეტული სერვისის ან პროექტის საკუთარ ფოტოს.')
                ->schema([
                    self::imageUpload('page_services_hero', 'services_hero', 'სერვისების გვერდის Hero სურათი', 'სწორედ ეს სურათი შეავსებს ახლა ცარიელ მარცხენა მხარეს /services გვერდზე.'),
                    self::imageUpload('page_projects_hero', 'projects_hero', 'პროექტების გვერდის Hero სურათი', 'გამოჩნდება /projects გვერდის ზედა ფონურ Hero ბლოკში.'),
                ])
                ->columns(2),

            Section::make('ჩვენს შესახებ')
                ->description('ჩვენს შესახებ გვერდის ისტორიის ბლოკის დამოუკიდებელი ფოტო.')
                ->schema([
                    self::imageUpload('page_about_story', 'about_story', 'ისტორიის სურათი', 'გამოჩნდება „ჩვენს შესახებ“ გვერდის ისტორიის სექციაში.'),
                ]),

            Section::make('კონტაქტი')
                ->description('Contact გვერდზე ორი სხვადასხვა ვიზუალური ბლოკია და თითოეულს საკუთარი ფოტო აქვს.')
                ->schema([
                    self::imageUpload('page_contact_intro', 'contact_intro', 'Contact — შესავალი სურათი', 'გამოჩნდება საკონტაქტო გვერდის შესავალ ბლოკში.'),
                    self::imageUpload('page_contact_support', 'contact_support', 'Contact — მხარდაჭერის სურათი', 'გამოჩნდება მხარდაჭერის/სერვისის ბლოკში.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('კონფიგურაცია')
                    ->formatStateUsing(fn (): string => 'SafeTech გვერდების სურათები'),
                TextColumn::make('updated_at')
                    ->label('ბოლო განახლება')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('სურათების მართვა'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('key', 'branding');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageImages::route('/'),
            'edit' => Pages\EditPageImages::route('/{record}/edit'),
        ];
    }

    private static function imageUpload(
        string $name,
        string $collection,
        string $label,
        string $helperText,
    ): SpatieMediaLibraryFileUpload {
        return SpatieMediaLibraryFileUpload::make($name)
            ->label($label)
            ->collection($collection)
            ->conversion('webp')
            ->image()
            ->imageEditor()
            ->maxSize(10240)
            ->helperText($helperText);
    }
}
