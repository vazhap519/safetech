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
        $consultationOpenQuery = AnalyticsEvent::query()->forType(AnalyticsEvent::TYPE_CONSULTATION_OPEN);
        $trackedLeadQuery = AnalyticsEvent::query()->leadCreated();
        $vitals = AnalyticsEvent::query()
            ->forType(AnalyticsEvent::TYPE_WEB_VITAL)
            ->where('created_at', '>=', now()->subDays(28))
            ->latest()->limit(10000)->get(['meta'])
            ->groupBy(fn (AnalyticsEvent $event): string => (string) data_get($event->meta, 'metric'));
        $p75 = static function (string $metric) use ($vitals): ?float {
            $values = $vitals->get($metric, collect())->map(fn (AnalyticsEvent $event): float => (float) data_get($event->meta, 'value'))->sort()->values();
            if ($values->isEmpty()) {
                return null;
            }

            return (float) $values[(int) ceil($values->count() * .75) - 1];
        };

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
            Stat::make('კონსულტაციის გახსნა', (clone $consultationOpenQuery)->count())
                ->description('CTA-დან გახსნილი ფორმა')
                ->color('info'),
            Stat::make('დაფიქსირებული ლიდები', (clone $trackedLeadQuery)->count())
                ->description('analytics consent-ის მქონე მოთხოვნები')
                ->color('success'),
            Stat::make('WhatsApp დაკლიკებები', (clone $whatsAppClickQuery)->count())
                ->color('success'),
            Stat::make('WhatsApp უნიკალური დამკლები', (clone $whatsAppClickQuery)->distinct()->count('visitor_hash'))
                ->description('ბრაუზერები, რომლებმაც დააწკაპეს')
                ->color('info'),
            Stat::make('LCP p75 · 28 დღე', ($p75('LCP') !== null ? number_format($p75('LCP')) : '—').' ms')
                ->description('რეალური მომხმარებლების RUM'),
            Stat::make('INP p75 · 28 დღე', ($p75('INP') !== null ? number_format($p75('INP')) : '—').' ms')
                ->description('რეალური მომხმარებლების RUM'),
            Stat::make('CLS p75 · 28 დღე', $p75('CLS') !== null ? number_format($p75('CLS'), 3) : '—')
                ->description('რეალური მომხმარებლების RUM'),
        ];
    }
}
