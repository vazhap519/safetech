<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LocalSeoOpportunityTable extends TableWidget
{
    protected static ?string $heading = 'Local SEO შესაძლებლობები — რა გავაკეთოთ შემდეგ';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
                    ->publiclyVisible()
                    ->with('category')
                    ->withAnalyticsSummary()
                    ->withCount([
                        'localServiceLandings as local_pages_count',
                        'localServiceLandings as indexable_local_pages_count' => fn ($query) => $query
                            ->publiclyVisible()
                            ->where('noindex', false),
                    ])
                    ->whereDoesntHave(
                        'localServiceLandings',
                        fn ($query) => $query->publiclyVisible()->where('noindex', false),
                    ),
            )
            ->defaultSort('unique_viewers_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('სერვისი')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('კატეგორია'),
                Tables\Columns\TextColumn::make('unique_viewers_count')
                    ->label('უნიკალური ნახვები')
                    ->numeric()
                    ->sortable()
                    ->description('მოთხოვნის პრიორიტეტი'),
                Tables\Columns\TextColumn::make('whatsapp_clicks_count')
                    ->label('WhatsApp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('local_pages_count')
                    ->label('Local დრაფტები')
                    ->numeric()
                    ->description('არსებული, მაგრამ ჯერ არაინდექსირებადი'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('სერვისის slug')
                    ->copyable()
                    ->searchable(),
            ])
            ->emptyStateHeading('ყველა გამოქვეყნებულ სერვისს აქვს ინდექსირებადი Local SEO გვერდი')
            ->emptyStateDescription('შემდეგი ფოკუსი გადაიტანეთ გვერდების ხარისხზე, პროექტებზე და კონვერსიაზე.')
            ->paginated([10, 25, 50]);
    }
}
