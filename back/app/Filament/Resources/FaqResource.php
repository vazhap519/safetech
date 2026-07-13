<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationLabel = 'FAQ';

    /** @return array<int, Textarea> */
    private static function translationTextareas(string $field, string $label): array
    {
        return collect([
            'ka' => 'ქართული',
            'en' => 'English',
            'ru' => 'Русский',
        ])->map(
            fn (string $localeLabel, string $locale) => Textarea::make("translations.fields.{$field}.{$locale}")
                ->label("{$label} ({$localeLabel})")
                ->rows(3),
        )->values()->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('FAQ ძირითადი ინფორმაცია')
                ->schema([
                    Select::make('service_id')
                        ->label('სერვისი')
                        ->relationship('service', 'name')
                        ->searchable()
                        ->preload(),
                    TextInput::make('context')
                        ->label('კონტექსტი')
                        ->default('general')
                        ->required(),
                    Textarea::make('question')
                        ->label('კითხვა')
                        ->required(),
                    Textarea::make('answer')
                        ->label('პასუხი')
                        ->required()
                        ->rows(6),
                    Toggle::make('is_active')
                        ->label('აქტიური')
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label('რიგითობა')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),

            Section::make('კითხვა/პასუხი 3 ენაზე')
                ->description('ძირითადი ველები რჩება ქართულ fallback-ად. service FAQ-სთვის ფრონტი ავტომატურად გამოიყენებს service.{slug}.faq.{index}.question/answer key-ებს.')
                ->schema([
                    ...self::translationTextareas('question', 'კითხვა'),
                    ...self::translationTextareas('answer', 'პასუხი'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->searchable()->limit(70),
                TextColumn::make('service.name')->label('სერვისი'),
                TextColumn::make('context')->label('კონტექსტი'),
                IconColumn::make('is_active')->label('აქტიური')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
