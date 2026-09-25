<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\GoogleBusinessServicesSeeder;
use Database\Seeders\PriorityLocalSeoCopy;
use Database\Seeders\PriorityLocalSeoSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityLocalSeoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_catalog_services_have_six_priority_city_pages_without_batumi(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);
        $this->seed(PriorityLocalSeoSeeder::class);

        $serviceIds = Service::query()->publiclyVisible()->pluck('id')->all();
        $this->assertCount(79, $serviceIds);

        foreach (PriorityLocalSeoCopy::CITY_ORDER as $city) {
            $this->assertSame(
                count($serviceIds),
                LocalServiceLanding::query()->where('location_slug', $city)
                    ->whereIn('service_id', $serviceIds)->distinct('service_id')->count('service_id'),
                $city
            );
        }

        $this->assertDatabaseMissing('local_service_landings', ['location_slug' => 'batumi']);

        $barrier = Service::query()->where('slug', 'barrier-gate-installation')->sole();
        $abastumani = LocalServiceLanding::query()
            ->where('service_id', $barrier->getKey())
            ->where('location_slug', 'abastumani')
            ->sole();

        $this->assertTrue($abastumani->is_published);
        $this->assertFalse($abastumani->noindex);
        $this->assertCount(0, $abastumani->projects);
        $this->assertGreaterThan(400, mb_strlen($abastumani->content));
        $this->assertCount(3, $abastumani->benefits);
        $this->assertGreaterThanOrEqual(2, count($abastumani->faq));
        $this->assertStringContainsString('დასასვენებელი', $abastumani->content);
        $this->assertStringContainsString(
            'holiday',
            mb_strtolower(data_get($abastumani->translations, 'fields.content.en')),
        );

        foreach (['ka', 'en', 'ru'] as $locale) {
            foreach ([
                'locationName', 'eyebrow', 'title', 'excerpt', 'content',
                'ctaTitle', 'ctaText', 'primaryKeyword',
                'seoTitle', 'seoDescription', 'ogTitle', 'ogDescription',
            ] as $field) {
                $this->assertNotEmpty(data_get($abastumani->translations, "fields.{$field}.{$locale}"));
            }
            $this->assertLessThanOrEqual(
                320,
                mb_strlen(data_get($abastumani->translations, "fields.seoDescription.{$locale}")),
            );
            $this->assertNotEmpty(data_get($abastumani->translations, "keywords.{$locale}"));
        }
    }

    public function test_short_noindexed_services_are_prepared_as_drafts_not_published_at_scale(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);
        $this->seed(PriorityLocalSeoSeeder::class);

        $shortSlug = collect(GoogleBusinessServicesSeeder::canonicalServiceSlugs())
            ->first(fn (string $slug): bool => ! in_array($slug, ServiceCatalogSeeder::canonicalServiceSlugs(), true));
        $this->assertNotNull($shortSlug);

        $service = Service::query()->where('slug', $shortSlug)->sole();
        $this->assertTrue((bool) data_get($service->seo, 'noindex'));

        $page = LocalServiceLanding::query()
            ->where('service_id', $service->getKey())
            ->where('location_slug', 'abastumani')->sole();

        $this->assertFalse($page->is_published);
        $this->assertTrue($page->noindex);
        $this->assertNull($page->published_at);
        $this->assertNotEmpty(data_get($page->translations, 'fields.content.ru'));
    }

    public function test_it_is_idempotent_and_preserves_existing_editorial_draft(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);

        $service = Service::query()->where('slug', 'barrier-gate-installation')->sole();
        $draft = LocalServiceLanding::query()->create([
            'service_id' => $service->getKey(),
            'location_slug' => 'abastumani',
            'location_name' => 'აბასთუმანი',
            'title' => 'რედაქტორის დაწერილი სათაური',
            'content' => 'რედაქტორის დაწერილი ტექსტი',
            'benefits' => [['title' => 'Custom', 'description' => 'Keep']],
            'is_published' => false,
            'noindex' => true,
            'translations' => ['fields' => ['title' => ['en' => 'Author title']]],
        ]);

        $this->seed(PriorityLocalSeoSeeder::class);
        $count = LocalServiceLanding::query()->count();
        $this->seed(PriorityLocalSeoSeeder::class);

        $draft->refresh();
        $this->assertDatabaseCount('local_service_landings', $count);
        $this->assertSame('რედაქტორის დაწერილი სათაური', $draft->title);
        $this->assertSame('რედაქტორის დაწერილი ტექსტი', $draft->content);
        $this->assertSame('Author title', data_get($draft->translations, 'fields.title.en'));
        $this->assertSame('Custom', $draft->benefits[0]['title']);
        $this->assertFalse($draft->is_published);
        $this->assertTrue($draft->noindex);
    }

    public function test_city_and_service_copy_is_not_one_city_swapped_template(): void
    {
        $this->seed(SystemContentSeeder::class);
        $this->seed(GoogleBusinessServicesSeeder::class);
        $this->seed(PriorityLocalSeoSeeder::class);

        $barrier = Service::query()->where('slug', 'barrier-gate-installation')->sole();
        $wifi = Service::query()->where('slug', 'router-wifi-configuration')->sole();

        $barrierBakuriani = LocalServiceLanding::query()->where('service_id', $barrier->id)
            ->where('location_slug', 'bakuriani')->sole();
        $wifiBakuriani = LocalServiceLanding::query()->where('service_id', $wifi->id)
            ->where('location_slug', 'bakuriani')->sole();
        $barrierTbilisi = LocalServiceLanding::query()->where('service_id', $barrier->id)
            ->where('location_slug', 'tbilisi')->sole();

        $this->assertNotSame($barrierBakuriani->content, $wifiBakuriani->content);
        $this->assertNotSame(
            data_get($barrierBakuriani->translations, 'fields.content.en'),
            data_get($barrierTbilisi->translations, 'fields.content.en'),
        );
    }
}
