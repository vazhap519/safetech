<?php

namespace App\Filament\Widgets;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LocalSeoStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $publishedServices = Service::query()->publiclyVisible()->count();
        $coveredServices = Service::query()
            ->publiclyVisible()
            ->whereHas(
                'localServiceLandings',
                fn ($query) => $query->publiclyVisible()->where('noindex', false),
            )
            ->count();
        $indexablePages = LocalServiceLanding::query()
            ->publiclyVisible()
            ->where('noindex', false)
            ->count();
        $liveNoindexPages = LocalServiceLanding::query()
            ->publiclyVisible()
            ->where('noindex', true)
            ->count();
        $drafts = LocalServiceLanding::query()
            ->where('is_published', false)
            ->count();
        $withoutProjectProof = LocalServiceLanding::query()
            ->publiclyVisible()
            ->where('noindex', false)
            ->whereDoesntHave('publicProjects')
            ->count();
        $opportunities = max(0, $publishedServices - $coveredServices);
        $coverage = $publishedServices > 0
            ? round(($coveredServices / $publishedServices) * 100)
            : 0;

        return [
            Stat::make('Local SEO დაფარვა', "{$coveredServices} / {$publishedServices}")
                ->description("{$coverage}% გამოქვეყნებული სერვისებისა")
                ->color($coverage >= 70 ? 'success' : ($coverage >= 30 ? 'warning' : 'danger')),
            Stat::make('ინდექსირებადი Local გვერდები', $indexablePages)
                ->description('Published + noindex გამორთული')
                ->color('success'),
            Stat::make('SEO შესაძლებლობები', $opportunities)
                ->description('სერვისები ინდექსირებადი Local გვერდის გარეშე')
                ->color($opportunities > 0 ? 'warning' : 'success'),
            Stat::make('რეალური პროექტის გარეშე', $withoutProjectProof)
                ->description('ინდექსირებადი Local გვერდები proof-ის გარეშე')
                ->color($withoutProjectProof > 0 ? 'warning' : 'success'),
            Stat::make('Live, მაგრამ noindex', $liveNoindexPages)
                ->description('კონტენტის დასრულების შემდეგ გადაამოწმეთ')
                ->color($liveNoindexPages > 0 ? 'info' : 'success'),
            Stat::make('Local SEO დრაფტები', $drafts)
                ->description('ჯერ არ გამოქვეყნებული გვერდები'),
        ];
    }
}
