<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Support\LocalizedContentFields;
use App\Filament\Support\NavigationGroup;
use App\Models\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
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

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationLabel = 'გუნდი';

    protected static ?string $modelLabel = 'თანამშრომელი';

    protected static ?string $pluralModelLabel = 'გუნდის წევრები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('თანამშრომლის ძირითადი ინფორმაცია')
                ->schema([
                    TextInput::make('first_name')->label('სახელი')->required(),
                    TextInput::make('last_name')->label('გვარი')->required(),
                    TextInput::make('position')->label('თანამდებობა')->required(),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->label('ფოტო')
                        ->collection('image')
                        ->conversion('webp')
                        ->image()
                        ->imageEditor()
                        ->maxSize(10240),
                    Textarea::make('bio')->label('ბიოგრაფია'),
                    KeyValue::make('socials')
                        ->label('სოციალური ბმულები')
                        ->keyLabel('ქსელი')
                        ->valueLabel('URL'),
                    Toggle::make('is_active')->label('აქტიური')->default(true),
                    TextInput::make('sort_order')->label('რიგითობა')->numeric()->default(0),
                ])
                ->columns(2),

            Section::make('გუნდის კონტენტი 3 ენაზე')
                ->description('ფრონტი გამოიყენებს team.{id}.firstName/lastName/position/bio key-ებს.')
                ->schema([
                    ...LocalizedContentFields::inputs('firstName', 'სახელი'),
                    ...LocalizedContentFields::inputs('lastName', 'გვარი'),
                    ...LocalizedContentFields::inputs('position', 'თანამდებობა'),
                    ...LocalizedContentFields::inputs('bio', 'ბიოგრაფია', textarea: true),
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
                    ->getStateUsing(fn (TeamMember $record): ?string => $record->image)
                    ->circular(),
                TextColumn::make('first_name')->label('სახელი')->searchable(),
                TextColumn::make('last_name')->label('გვარი')->searchable(),
                TextColumn::make('position')->label('თანამდებობა'),
                IconColumn::make('is_active')->label('აქტიური')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
