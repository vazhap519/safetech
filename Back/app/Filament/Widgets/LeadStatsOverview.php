<?php

namespace App\Filament\Widgets;

use App\Models\ContactLead;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('ახალი მოთხოვნები', ContactLead::query()->where('status', 'new')->count())->color('warning'),
            Stat::make('ამ თვის მოთხოვნები', ContactLead::query()->where('created_at', '>=', now()->startOfMonth())->count()),
            Stat::make('გამოქვეყნებული სერვისები', Service::query()->where('is_published', true)->count())->color('success'),
            Stat::make('გამოქვეყნებული პროექტები', Project::query()->where('is_published', true)->count())->color('success'),
        ];
    }
}
