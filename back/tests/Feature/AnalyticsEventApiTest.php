<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsEventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_service_view_event(): void
    {
        $service = Service::query()->create([
            'slug' => 'networking',
            'name' => 'Network Infrastructure',
            'title' => 'Network Infrastructure',
            'description' => 'Test description',
            'seo_description' => 'Test SEO description',
            'is_published' => true,
        ]);

        $response = $this
            ->withHeader('User-Agent', 'PHPUnit Analytics Test')
            ->postJson('/api/analytics/events', [
                'event_type' => AnalyticsEvent::TYPE_SERVICE_VIEW,
                'service_slug' => 'networking',
                'page_path' => '/services/networking?email=private@example.com',
                'visitor_id' => 'browser-123',
                'locale' => 'ka',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.tracked', true)
            ->assertJsonPath('data.eventType', AnalyticsEvent::TYPE_SERVICE_VIEW);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => AnalyticsEvent::TYPE_SERVICE_VIEW,
            'service_id' => $service->id,
            'service_slug' => 'networking',
            'page_path' => '/services/networking',
            'locale' => 'ka',
        ]);

        $event = AnalyticsEvent::query()->latest('id')->firstOrFail();

        $this->assertNotSame('browser-123', $event->visitor_hash);
    }

    public function test_it_links_whatsapp_clicks_to_service_path(): void
    {
        $service = Service::query()->create([
            'slug' => 'cctv',
            'name' => 'CCTV',
            'title' => 'CCTV',
            'description' => 'Test description',
            'seo_description' => 'Test SEO description',
            'is_published' => true,
        ]);

        $response = $this->postJson('/api/analytics/events', [
            'event_type' => AnalyticsEvent::TYPE_WHATSAPP_CLICK,
            'page_path' => '/services/cctv',
            'visitor_id' => 'browser-456',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => AnalyticsEvent::TYPE_WHATSAPP_CLICK,
            'service_id' => $service->id,
            'service_slug' => 'cctv',
            'page_path' => '/services/cctv',
        ]);
    }
}
