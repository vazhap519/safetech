<?php

namespace App\Filament\Support;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CategoryTable
{
    public static function configure(
        Table $table,
        bool $reorderable = false,
        bool $showIcon = false,
    ): Table {
        if ($reorderable) {
            $table
                ->reorderable('sort_order')
                ->defaultSort('sort_order');
        }

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('დასახელება')
                    ->formatStateUsing(
                        fn (string $state, $record): string => $showIcon && $record->icon
                            ? "{$record->icon} {$state}"
                            : $state,
                    )
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('URL კოდი')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                ...($reorderable ? [
                    TextColumn::make('sort_order')
                        ->label('რიგითობა')
                        ->sortable(),
                ] : []),
                IconColumn::make('noindex')
                    ->label('ინდექსაცია გამორთულია')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('შექმნის თარიღი')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
