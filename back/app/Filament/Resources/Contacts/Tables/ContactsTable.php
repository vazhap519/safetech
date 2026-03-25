<?php

namespace App\Filament\Resources\Contacts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s') // 🔥 ყოველ 5 წამში refresh

            ->columns([

                TextColumn::make('name')
                    ->label('სახელი')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => !$record->is_read ? 'success' : 'gray'),

                TextColumn::make('phone')
                    ->label('ტელეფონი')
                    ->formatStateUsing(fn ($state) => "+995 {$state}")
                    ->url(fn ($record) => "tel:+995{$record->phone}")
                    ->openUrlInNewTab(),

                TextColumn::make('message')
                    ->label('მესიჯი')
                    ->limit(40),

                TextColumn::make('created_at')
                    ->label('თარიღი')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

            ])

            ->defaultSort('created_at', 'desc')

            ->rowClasses(fn ($record) => !$record->is_read ? 'bg-green-50' : null)

            ->filters([
                //
            ])

            ->recordActions([
                ViewAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
