<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationLabel = 'შეფასებები';

    /** @return array<int, TextInput|Textarea> */
    private static function translationInputs(string $field, string $label, bool $textarea = false): array
    {
        return collect([
            'ka' => 'ქართული',
            'en' => 'English',
            'ru' => 'Русский',
        ])->map(
            fn (string $localeLabel, string $locale) => ($textarea
                ? Textarea::make("translations.fields.{$field}.{$locale}")->rows(4)
                : TextInput::make("translations.fields.{$field}.{$locale}")
            )->label("{$label} ({$localeLabel})"),
        )->values()->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('შეფასების ძირითადი ინფორმაცია')
                ->schema([
                    Textarea::make('quote')->label('შეფასება')->required()->rows(5),
                    TextInput::make('author')->label('ავტორი')->required(),
                    TextInput::make('role')->label('თანამდებობა'),
                    TextInput::make('company')->label('კომპანია'),
                    FileUpload::make('image')
                        ->label('ფოტო')
                        ->image()
                        ->disk('public')
                        ->directory('testimonials'),
                    Toggle::make('is_active')->label('აქტიური')->default(true),
                    TextInput::make('sort_order')->label('რიგითობა')->numeric()->default(0),
                ])
                ->columns(2),

            Section::make('შეფასება 3 ენაზე')
                ->description('ფრონტისთვის ხელმისაწვდომია testimonial.{id}.quote/author/role/company key-ებით.')
                ->schema([
                    ...self::translationInputs('quote', 'შეფასება', true),
                    ...self::translationInputs('author', 'ავტორი'),
                    ...self::translationInputs('role', 'თანამდებობა'),
                    ...self::translationInputs('company', 'კომპანია'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author')->searchable(),
                TextColumn::make('company'),
                TextColumn::make('quote')->limit(60),
                IconColumn::make('is_active')->label('აქტიური')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
