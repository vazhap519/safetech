<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\LocalSeoFieldCompletionSeeder;
use Database\Seeders\LocalServiceCoverageSeeder;
use Database\Seeders\PriorityCityLocalSeoSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityCityLocalSeoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_trilingual_drafts_in_priority_cities_without_publishing_templates(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(LocalServiceCoverageSeeder::class);

        $service = Service::query()->where('slug', 'ip-camera-installation')->sole();
        $existing = LocalServiceLanding::query()
            ->where('service_id', $service->id)
            ->where('location_slug', 'tbilisi')
            ->sole();
        $existing->update(['title' => 'CMS authored title']);

        $this->seed(PriorityCityLocalSeoSeeder::class);
        $count = LocalServiceLanding::query()->count();
        $this->seed(PriorityCityLocalSeoSeeder::class);
        $this->seed(LocalSeoFieldCompletionSeeder::class);

        $this->assertDatabaseCount('local_service_landings', $count);
        $this->assertSame('CMS authored title', $existing->fresh()->title);
        $this->assertSame(0, LocalServiceLanding::query()->where('location_slug', 'batumi')->count());

        foreach (['bakuriani', 'surami', 'borjomi', 'khashuri', 'abastumani'] as $city) {
            $landing = LocalServiceLanding::query()
                ->where('service_id', $service->id)
                ->where('location_slug', $city)
                ->sole();

            $this->assertFalse($landing->is_published);
            $this->assertTrue($landing->noindex);
            $this->assertNull($landing->published_at);
            $this->assertNotEmpty($landing->content);
            $this->assertNotEmpty($landing->benefits);
            $this->assertNotEmpty($landing->faq);

            foreach (['ka', 'en', 'ru'] as $locale) {
                foreach (['title', 'content', 'seoTitle', 'seoDescription', 'ogTitle', 'ogDescription', 'ctaTitle', 'ctaText'] as $field) {
                    $this->assertNotEmpty(data_get($landing->translations, "fields.{$field}.{$locale}"), "{$city}.{$locale}.{$field}");
                }
            }
        }
    }

    public function test_it_does_not_create_city_pages_for_services_without_real_translations(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $service = Service::query()->where('slug', 'ip-camera-installation')->sole();
        $translations = $service->translations;
        data_set($translations, 'fields.description.ru', '');
        $service->translations = $translations;
        $service->save();

        $this->seed(PriorityCityLocalSeoSeeder::class);

        $this->assertSame(0, LocalServiceLanding::query()
            ->where('service_id', $service->id)->count());
    }
}
