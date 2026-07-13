<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ServiceEngagementTable extends TableWidget
{
    protected static ?string $heading = 'სერვისების ჩართულობა';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
                    ->where('is_published', true)
                    ->withAnalyticsSummary(),
            )
            ->defaultSort('total_views_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('სერვისი')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unique_viewers_count')
                    ->label('უნიკალური ნახვები')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_views_count')
                    ->label('სულ ნახვები')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_clicks_count')
                    ->label('WhatsApp კლიკები')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_whatsapp_clickers_count')
                    ->label('უნიკალური დამკლები')
                    ->numeric()
                    ->sortable(),
            ])
            ->paginated([10, 25, 50]);
    }
}
