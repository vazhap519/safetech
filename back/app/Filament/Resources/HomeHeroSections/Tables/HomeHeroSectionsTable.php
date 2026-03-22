<?php

namespace App\Filament\Resources\HomeHeroSections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class HomeHeroSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
TextColumn::make('home_hero_title')->label('სათაური'),
TextColumn::make('home_hero_description')->label('აღწერა'),
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
