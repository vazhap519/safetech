<?php

namespace App\Filament\Resources\SeoPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

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

                IconColumn::make('noindex')
                    ->boolean()
                    ->label('NoIndex'),

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
