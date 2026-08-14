<?php

namespace App\Support\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\LocalServiceLanding;
use Illuminate\Database\Eloquent\Builder;

final class LocalLandingAnalytics
{
    /** @return array{views:int,unique_viewers:int,consultation_opens:int,whatsapp_clicks:int,phone_clicks:int,leads:int,lead_conversion_rate:float} */
    public function summary(LocalServiceLanding $landing): array
    {
        $serviceSlug = trim((string) $landing->service?->slug);
        $locationSlug = trim((string) $landing->location_slug);

        if ($serviceSlug === '' || $locationSlug === '') {
            return $this->emptySummary();
        }

        $path = '/services/'.$serviceSlug.'/'.$locationSlug;
        $paths = [
            $path,
            '/ka'.$path,
            '/en'.$path,
            '/ru'.$path,
        ];

        $row = AnalyticsEvent::query()
            ->whereIn('page_path', $paths)
            ->selectRaw(
                'SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) AS views',
                [AnalyticsEvent::TYPE_SERVICE_VIEW],
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN event_type = ? THEN visitor_hash END) AS unique_viewers',
                [AnalyticsEvent::TYPE_SERVICE_VIEW],
            )
            ->selectRaw(
                'SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) AS consultation_opens',
                [AnalyticsEvent::TYPE_CONSULTATION_OPEN],
            )
            ->selectRaw(
                'SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) AS whatsapp_clicks',
                [AnalyticsEvent::TYPE_WHATSAPP_CLICK],
            )
            ->selectRaw(
                'SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) AS phone_clicks',
                [AnalyticsEvent::TYPE_PHONE_CLICK],
            )
            ->selectRaw(
                'SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) AS leads',
                [AnalyticsEvent::TYPE_LEAD_CREATED],
            )
            ->first();

        $uniqueViewers = (int) ($row?->unique_viewers ?? 0);
        $leads = (int) ($row?->leads ?? 0);

        return [
            'views' => (int) ($row?->views ?? 0),
            'unique_viewers' => $uniqueViewers,
            'consultation_opens' => (int) ($row?->consultation_opens ?? 0),
            'whatsapp_clicks' => (int) ($row?->whatsapp_clicks ?? 0),
            'phone_clicks' => (int) ($row?->phone_clicks ?? 0),
            'leads' => $leads,
            'lead_conversion_rate' => $uniqueViewers > 0
                ? round(($leads / $uniqueViewers) * 100, 1)
                : 0.0,
        ];
    }

    /** @return array{views:int,unique_viewers:int,consultation_opens:int,whatsapp_clicks:int,phone_clicks:int,leads:int,lead_conversion_rate:float} */
    private function emptySummary(): array
    {
        return [
            'views' => 0,
            'unique_viewers' => 0,
            'consultation_opens' => 0,
            'whatsapp_clicks' => 0,
            'phone_clicks' => 0,
            'leads' => 0,
            'lead_conversion_rate' => 0.0,
        ];
    }
}
