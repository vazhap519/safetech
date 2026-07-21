<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'ადმინისტრატორები';

    protected static ?string $modelLabel = 'ადმინისტრატორი';

    protected static ?string $pluralModelLabel = 'ადმინისტრატორები';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::System;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('სახელი')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('ელფოსტა')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('password')
                ->label('პაროლი')
                ->password()
                ->revealable(false)
                ->autocomplete('new-password')
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                ->minLength(12),
            Toggle::make('is_admin')
                ->label('ადმინისტრატორის წვდომა')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('სახელი')->searchable(),
                TextColumn::make('email')->label('ელფოსტა')->searchable(),
                IconColumn::make('is_admin')->label('ადმინისტრატორი')->boolean(),
                TextColumn::make('created_at')->label('შექმნის თარიღი')->dateTime()->sortable(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
