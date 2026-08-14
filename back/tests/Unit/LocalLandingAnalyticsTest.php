<?php

namespace Tests\Unit;

use App\Models\AnalyticsEvent;
use App\Models\LocalServiceLanding;
use App\Models\Service;
use App\Support\Analytics\LocalLandingAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalLandingAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_attributes_local_landing_conversion_events_across_locales(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation',
            'name' => 'უსაფრთხოების კამერების მონტაჟი',
            'title' => 'უსაფრთხოების კამერების მონტაჟი',
            'description' => 'კამერების პროფესიონალური მონტაჟი.',
            'seo_description' => 'კამერების მონტაჟი.',
            'is_published' => true,
        ]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'თბილისი',
            'title' => 'უსაფრთხოების კამერების მონტაჟი თბილისში',
            'content' => 'უნიკალური ადგილობრივი კონტენტი.',
            'is_published' => true,
            'noindex' => false,
        ]);

        $this->event(AnalyticsEvent::TYPE_SERVICE_VIEW, '/services/security-camera-installation/tbilisi', 'visitor-a', $service->id);
        $this->event(AnalyticsEvent::TYPE_SERVICE_VIEW, '/en/services/security-camera-installation/tbilisi', 'visitor-b', $service->id);
        $this->event(AnalyticsEvent::TYPE_SERVICE_VIEW, '/ru/services/security-camera-installation/tbilisi', 'visitor-a', $service->id);
        $this->event(AnalyticsEvent::TYPE_CONSULTATION_OPEN, '/en/services/security-camera-installation/tbilisi', 'visitor-b', $service->id);
        $this->event(AnalyticsEvent::TYPE_WHATSAPP_CLICK, '/services/security-camera-installation/tbilisi', 'visitor-a', $service->id);
        $this->event(AnalyticsEvent::TYPE_PHONE_CLICK, '/ru/services/security-camera-installation/tbilisi', 'visitor-a', $service->id);
        $this->event(AnalyticsEvent::TYPE_LEAD_CREATED, '/en/services/security-camera-installation/tbilisi', 'visitor-b', $service->id);
        $this->event(AnalyticsEvent::TYPE_SERVICE_VIEW, '/services/security-camera-installation/batumi', 'visitor-c', $service->id);

        $summary = app(LocalLandingAnalytics::class)->summary($landing->load('service'));

        $this->assertSame(3, $summary['views']);
        $this->assertSame(2, $summary['unique_viewers']);
        $this->assertSame(1, $summary['consultation_opens']);
        $this->assertSame(1, $summary['whatsapp_clicks']);
        $this->assertSame(1, $summary['phone_clicks']);
        $this->assertSame(1, $summary['leads']);
        $this->assertSame(50.0, $summary['lead_conversion_rate']);
    }

    private function event(string $type, string $path, string $visitor, int $serviceId): void
    {
        AnalyticsEvent::query()->create([
            'event_type' => $type,
            'service_id' => $serviceId,
            'service_slug' => 'security-camera-installation',
            'page_path' => $path,
            'locale' => 'ka',
            'visitor_hash' => hash('sha256', $visitor),
            'ip_hash' => hash('sha256', '127.0.0.1'),
            'user_agent' => 'PHPUnit',
        ]);
    }
}
