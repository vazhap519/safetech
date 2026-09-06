<?php

namespace App\Filament\Resources\SeoPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('გვერდი')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL'),

                TextColumn::make('title')
                    ->label('SEO სათაური')
                    ->limit(40),

                TextColumn::make('noindex')
                    ->label('Google ინდექსაცია')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'გამორთულია' : 'ჩართულია')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('განახლდა')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
