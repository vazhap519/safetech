<?php

namespace App\Filament\Resources\HomeStats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HomeStatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ToggleColumn::make('is_active')
                    ->label('აქტიური'),

                TextColumn::make('items')
                    ->label('სტატისტიკა')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state) {

                        if (is_string($state)) {
                            $state = json_decode($state, true);
                        }

                        if (!is_array($state)) {
                            return '-';
                        }

                        return collect($state)
                            ->map(function ($item) {
                                return ($item['value'] ?? '') . ' - ' . ($item['label'] ?? '');
                            })
                            ->implode(', ');
                    }),

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
