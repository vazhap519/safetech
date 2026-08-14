<?php

namespace App\Filament\Widgets;

use App\Models\LocalServiceLanding;
use App\Support\Analytics\LocalLandingAnalytics;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LocalLandingPerformanceTable extends TableWidget
{
    protected static ?string $heading = 'Local SEO გვერდები — კონვერსიის შედეგები';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    /** @var array<int, array<string, int|float>> */
    private array $analyticsCache = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LocalServiceLanding::query()
                    ->with('service')
                    ->withCount([
                        'projects as published_projects_count' => fn ($query) => $query
                            ->where('projects.is_published', true),
                    ])
                    ->publiclyVisible(),
            )
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->label('სერვისი')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location_name')
                    ->label('ლოკაცია')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('indexing_status')
                    ->label('Google')
                    ->getStateUsing(fn (LocalServiceLanding $record): string => $record->noindex ? 'Noindex' : 'Index')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Index' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('published_projects_count')
                    ->label('რეალური პროექტები')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_local_viewers')
                    ->label('უნიკალური ნახვები')
                    ->getStateUsing(fn (LocalServiceLanding $record): int => (int) $this->analytics($record)['unique_viewers'])
                    ->numeric(),
                Tables\Columns\TextColumn::make('consultation_opens')
                    ->label('კონსულტაცია')
                    ->getStateUsing(fn (LocalServiceLanding $record): int => (int) $this->analytics($record)['consultation_opens'])
                    ->numeric(),
                Tables\Columns\TextColumn::make('whatsapp_clicks')
                    ->label('WhatsApp')
                    ->getStateUsing(fn (LocalServiceLanding $record): int => (int) $this->analytics($record)['whatsapp_clicks'])
                    ->numeric(),
                Tables\Columns\TextColumn::make('leads')
                    ->label('ლიდები')
                    ->getStateUsing(fn (LocalServiceLanding $record): int => (int) $this->analytics($record)['leads'])
                    ->numeric(),
                Tables\Columns\TextColumn::make('lead_conversion_rate')
                    ->label('Lead CVR')
                    ->getStateUsing(fn (LocalServiceLanding $record): float => (float) $this->analytics($record)['lead_conversion_rate'])
                    ->suffix('%')
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 5 => 'success',
                        $state > 0 => 'info',
                        default => 'gray',
                    }),
            ])
            ->emptyStateHeading('Local SEO გვერდები ჯერ არ არის გამოქვეყნებული')
            ->emptyStateDescription('შექმენით პირველი ხარისხიანი ქალაქი + სერვისი გვერდი და დატოვეთ noindex, სანამ კონტენტი სრულად არ დასრულდება.')
            ->paginated([10, 25, 50]);
    }

    /** @return array<string, int|float> */
    private function analytics(LocalServiceLanding $landing): array
    {
        return $this->analyticsCache[$landing->id] ??= app(LocalLandingAnalytics::class)
            ->summary($landing);
    }
}
