<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use App\Models\ContactLead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $serviceViewQuery = AnalyticsEvent::query()->serviceViews();
        $whatsAppClickQuery = AnalyticsEvent::query()->whatsAppClicks();

        return [
            Stat::make('ახალი მოთხოვნები', ContactLead::query()->where('status', 'new')->count())
                ->color('warning'),
            Stat::make('ამ თვის მოთხოვნები', ContactLead::query()->where('created_at', '>=', now()->startOfMonth())->count())
                ->description('ლიდები მიმდინარე თვეში'),
            Stat::make('სერვისების სულ ნახვები', (clone $serviceViewQuery)->count())
                ->color('primary'),
            Stat::make('სერვისების უნიკალური ნახვები', (clone $serviceViewQuery)->distinct()->count('visitor_hash'))
                ->description('უნიკალური ბრაუზერები')
                ->color('success'),
            Stat::make('WhatsApp დაკლიკებები', (clone $whatsAppClickQuery)->count())
                ->color('success'),
            Stat::make('WhatsApp უნიკალური დამკლები', (clone $whatsAppClickQuery)->distinct()->count('visitor_hash'))
                ->description('ბრაუზერები, რომლებმაც დააწკაპეს')
                ->color('info'),
        ];
    }
}
