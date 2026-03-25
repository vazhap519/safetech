<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\{
    TextColumn,
    ImageColumn,
    IconColumn
};
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\{
    BulkActionGroup,
    DeleteBulkAction,
    EditAction
};

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
                    ->label('Image')
                    ->getStateUsing(fn ($record) =>
                    $record->getFirstMediaUrl('cover', 'webp')
                    )
                    ->size(60),

                /*
                |--------------------------------------------------------------------------
                | TITLE
                |--------------------------------------------------------------------------
                */
                TextColumn::make('title')
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
                    ->label('Category')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | AUTHOR
                |--------------------------------------------------------------------------
                */
                TextColumn::make('author.name')
                    ->label('Author')
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                /*
                |--------------------------------------------------------------------------
                | DATE
                |--------------------------------------------------------------------------
                */
                TextColumn::make('created_at')
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
                    ->relationship('category', 'name'),

                /*
                |--------------------------------------------------------------------------
                | STATUS FILTER
                |--------------------------------------------------------------------------
                */
                SelectFilter::make('is_published')
                    ->options([
                        1 => 'Published',
                        0 => 'Draft',
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
