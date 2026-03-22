<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // 🖼️ სურათი
                ImageColumn::make('image')
                    ->label('სურათი')
                    ->getStateUsing(fn ($record) =>
                        $record->getFirstMediaUrl('services', 'webp')
                    )
                    ->circular(),

                // 📝 სათაური
                TextColumn::make('title')
                    ->label('სათაური')
                    ->searchable()
                    ->sortable(),

                // 🔗 slug
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                // 📞 ტელეფონი
                TextColumn::make('phone')
                    ->label('ტელეფონი')
                    ->toggleable(),

                // 🕒 შექმნის დრო
                TextColumn::make('created_at')
                    ->label('შექმნის თარიღი')
                    ->dateTime('d M Y')
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