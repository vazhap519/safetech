<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationLabel = 'შეფასებები';

    protected static ?string $modelLabel = 'კლიენტის შეფასება';

    protected static ?string $pluralModelLabel = 'კლიენტების შეფასებები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('შეფასების ძირითადი ინფორმაცია')
                ->schema([
                    Textarea::make('quote')->label('შეფასება')->required()->rows(5),
                    TextInput::make('author')->label('ავტორი')->required(),
                    TextInput::make('role')->label('თანამდებობა'),
                    TextInput::make('company')->label('კომპანია'),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('ფოტო')
                        ->collection('image')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    Toggle::make('is_active')->label('აქტიური')->default(true),
                    TextInput::make('sort_order')->label('რიგითობა')->numeric()->default(0),
                ])
                ->columns(2),

            Section::make('შეფასება 3 ენაზე')
                ->description('ფრონტისთვის ხელმისაწვდომია testimonial.{id}.quote/author/role/company key-ებით.')
                ->schema([
                    ...LocalizedContentFields::inputs('quote', 'შეფასება', textarea: true, rows: 4),
                    ...LocalizedContentFields::inputs('author', 'ავტორი'),
                    ...LocalizedContentFields::inputs('role', 'თანამდებობა'),
                    ...LocalizedContentFields::inputs('company', 'კომპანია'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('ფოტო')
                    ->getStateUsing(fn (Testimonial $record): ?string => $record->image)
                    ->circular(),
                TextColumn::make('author')->label('ავტორი')->searchable(),
                TextColumn::make('company')->label('კომპანია'),
                TextColumn::make('quote')->label('შეფასება')->limit(60),
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
