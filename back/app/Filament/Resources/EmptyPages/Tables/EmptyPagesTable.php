<?php

namespace App\Filament\Resources\EmptyPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmptyPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('socials')
                    ->label('Socials')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';

                        return collect($state)
                            ->map(fn ($item) => $item['icon'] ?? '—')
                            ->implode(', ');
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i')
                    ->label('Created'),
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
