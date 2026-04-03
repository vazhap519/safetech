<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                /* =========================
                   🖼 COVER IMAGE
                ========================= */
                ImageColumn::make('cover')
                    ->label('Image')
                    ->getStateUsing(fn ($record) => $record->thumb_url)
                    ->circular(),

                /* =========================
                   📝 TITLE
                ========================= */
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                /* =========================
                   🔗 SLUG
                ========================= */
                TextColumn::make('slug')
                    ->toggleable()
                    ->color('gray'),

                /* =========================
                   📂 CATEGORY
                ========================= */
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),

                /* =========================
                   📅 DATE
                ========================= */
                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),

                /* =========================
                   👁 STATUS
                ========================= */
                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),

            ])

            /* =========================
               🔍 FILTERS
            ========================= */
            ->filters([

                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('is_published')
                    ->options([
                        1 => 'Published',
                        0 => 'Draft',
                    ]),

            ])

            /* =========================
               ⚡ ACTIONS
            ========================= */
            ->recordActions([
                EditAction::make(),
            ])

            /* =========================
               🧹 BULK
            ========================= */
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('published_at', 'desc');
    }
}
