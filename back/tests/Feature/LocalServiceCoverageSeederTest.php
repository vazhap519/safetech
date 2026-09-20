<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\LocalServiceCoverageSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalServiceCoverageSeederTest extends TestCase
{
    use RefreshDatabase;

    private const MISSING_SLUGS = [
        'operating-system-installation',
        'custom-computer-build',
        'computer-cleaning-maintenance',
        'rack-assembly-cable-management',
        'pos-system-installation',
        'patch-panel-network-outlet-installation',
    ];

    public function test_it_completes_each_missing_service_with_editorial_trilingual_tbilisi_content(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(LocalServiceCoverageSeeder::class);
        $this->seed(LocalServiceCoverageSeeder::class);

        $this->assertDatabaseCount('services', 12);
        $this->assertDatabaseCount('local_service_landings', 6);
        $this->assertDatabaseCount('projects', 0);

        foreach (self::MISSING_SLUGS as $slug) {
            $service = Service::query()->where('slug', $slug)->firstOrFail();
            $landing = LocalServiceLanding::query()
                ->where('service_id', $service->id)
                ->where('location_slug', 'tbilisi')
                ->sole();

            $this->assertTrue($landing->is_published);
            $this->assertFalse($landing->noindex);
            $this->assertNotEmpty($landing->published_at);
            $this->assertGreaterThan(400, mb_strlen($landing->content));
            $this->assertCount(3, $landing->benefits);
            $this->assertCount(2, $landing->faq);
            $this->assertCount(0, $landing->projects);

            foreach (['ka', 'en', 'ru'] as $locale) {
                $this->assertGreaterThan(250, mb_strlen(data_get($landing->translations, "fields.content.{$locale}")));
                $this->assertNotEmpty(data_get($landing->translations, "fields.seoTitle.{$locale}"));
                $this->assertNotEmpty(data_get($landing->translations, "fields.seoDescription.{$locale}"));
                $this->assertNotEmpty(data_get($landing->translations, "fields.ctaText.{$locale}"));
            }

            $this->getJson("/api/local-service-landings/{$slug}/tbilisi?locale=en")
                ->assertOk()
                ->assertJsonPath('data.locationName', 'Tbilisi')
                ->assertJsonPath('data.seo.noindex', false)
                ->assertJsonCount(3, 'data.benefits')
                ->assertJsonCount(2, 'data.faqs');
            $this->getJson("/api/local-service-landings/{$slug}/tbilisi?locale=ru")
                ->assertOk()
                ->assertJsonPath('data.locationName', 'Тбилиси')
                ->assertJsonPath('data.seo.noindex', false);
        }

        $contents = LocalServiceLanding::query()->pluck('content')->all();
        $this->assertCount(6, array_unique($contents));
    }

    public function test_it_preserves_all_admin_managed_copy_and_manual_publication_decisions(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = Service::query()->where('slug', 'pos-system-installation')->firstOrFail();
        $manual = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'თბილისი',
            'title' => 'ხელით მომზადებული POS ტექსტი',
            'content' => 'ეს არის CMS-ში ადმინისტრატორის მიერ დაწერილი გვერდი.',
            'noindex' => true,
            'is_published' => false,
        ]);

        $this->seed(LocalServiceCoverageSeeder::class);

        $manual->refresh();
        $this->assertSame('ხელით მომზადებული POS ტექსტი', $manual->title);
        $this->assertTrue($manual->noindex);
        $this->assertFalse($manual->is_published);
        $this->assertDatabaseCount('local_service_landings', 6);
    }

    public function test_coverage_is_complete_when_other_existing_service_pages_are_indexable(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(LocalServiceCoverageSeeder::class);

        $existingServiceSlugs = array_diff(
            Service::query()->pluck('slug')->all(),
            self::MISSING_SLUGS,
        );
        foreach ($existingServiceSlugs as $slug) {
            $service = Service::query()->where('slug', $slug)->firstOrFail();
            LocalServiceLanding::query()->create([
                'service_id' => $service->id,
                'location_slug' => 'tbilisi',
                'location_name' => 'თბილისი',
                'title' => $service->name.' თბილისში',
                'content' => 'რეალური სერვისის ლოკალური აღწერა თბილისის მომხმარებლისთვის.',
                'is_published' => true,
                'noindex' => false,
            ]);
        }

        $publishedServices = Service::query()->publiclyVisible()->count();
        $covered = Service::query()->publiclyVisible()->whereHas('localServiceLandings',
            fn ($query) => $query->publiclyVisible()->where('noindex', false))->count();

        $this->assertSame(12, $publishedServices);
        $this->assertSame($publishedServices, $covered);
        $this->assertDatabaseCount('projects', 0);
    }
}
