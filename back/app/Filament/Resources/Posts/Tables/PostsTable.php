<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | IMAGE (Spatie)
                |--------------------------------------------------------------------------
                */
                ImageColumn::make('cover')
                    ->label('სურათი')
                    ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('cover', 'webp')
                    )
                    ->size(60),

                /*
                |--------------------------------------------------------------------------
                | TITLE
                |--------------------------------------------------------------------------
                */
                TextColumn::make('title')
                    ->label('სათაური')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),

                /*
                |--------------------------------------------------------------------------
                | CATEGORY
                |--------------------------------------------------------------------------
                */
                TextColumn::make('category.name')
                    ->label('კატეგორია')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | AUTHOR
                |--------------------------------------------------------------------------
                */
                TextColumn::make('author.name')
                    ->label('ავტორი')
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */
                IconColumn::make('is_published')
                    ->label('გამოქვეყნებულია')
                    ->boolean(),

                /*
                |--------------------------------------------------------------------------
                | DATE
                |--------------------------------------------------------------------------
                */
                TextColumn::make('created_at')
                    ->label('შექმნის თარიღი')
                    ->dateTime('d M Y')
                    ->sortable(),

            ])

            ->filters([

                /*
                |--------------------------------------------------------------------------
                | CATEGORY FILTER
                |--------------------------------------------------------------------------
                */
                SelectFilter::make('category')
                    ->label('კატეგორია')
                    ->relationship('category', 'name'),

                /*
                |--------------------------------------------------------------------------
                | STATUS FILTER
                |--------------------------------------------------------------------------
                */
                SelectFilter::make('is_published')
                    ->label('სტატუსი')
                    ->options([
                        1 => 'გამოქვეყნებული',
                        0 => 'შავი ვერსია',
                    ]),

            ])

            ->defaultSort('created_at', 'desc')

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
