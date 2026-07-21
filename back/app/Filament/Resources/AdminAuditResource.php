<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminAuditResource\Pages;
use App\Filament\Support\NavigationGroup;
use App\Models\AdminAudit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminAuditResource extends Resource
{
    protected static ?string $model = AdminAudit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'ცვლილებების ჟურნალი';

    protected static ?string $modelLabel = 'ცვლილება';

    protected static ?string $pluralModelLabel = 'ცვლილებების ჟურნალი';

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::System;

    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('თარიღი')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('ადმინისტრატორი')
                    ->placeholder('წაშლილი მომხმარებელი')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('მოქმედება')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'შექმნა',
                        'updated' => 'განახლება',
                        'deleted' => 'წაშლა',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'deleted' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('auditable_type')
                    ->label('მოდელი')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable(),
                TextColumn::make('label')
                    ->label('ჩანაწერი')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('changed_fields')
                    ->label('შეცვლილი ველები')
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP მისამართი')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('მოქმედება')
                    ->options([
                        'created' => 'შექმნა',
                        'updated' => 'განახლება',
                        'deleted' => 'წაშლა',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminAudits::route('/'),
        ];
    }
}
