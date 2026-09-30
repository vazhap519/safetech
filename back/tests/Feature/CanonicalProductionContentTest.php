<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\CanonicalLocalSeoSeeder;
use Database\Seeders\GoogleBusinessServiceDefinitions;
use Database\Seeders\PriorityLocalSeoCopy;
use Database\Seeders\ProductionContentSeeder;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalProductionContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seed_has_one_canonical_service_catalog_and_complete_locales(): void
    {
        $this->seed(ProductionContentSeeder::class);

        $canonicalSlugs = ServiceCatalogSeeder::canonicalServiceSlugs();
        $services = Service::query()->publiclyVisible()->get();

        $this->assertCount(count($canonicalSlugs), $services);
        $this->assertEqualsCanonicalizing($canonicalSlugs, $services->pluck('slug')->all());

        foreach (array_keys(GoogleBusinessServiceDefinitions::CANONICAL_ALIASES) as $alias) {
            $this->assertDatabaseMissing('services', ['slug' => $alias]);
        }

        foreach (PriorityLocalSeoCopy::CITY_ORDER as $city) {
            $this->assertSame(
                count($canonicalSlugs),
                LocalServiceLanding::query()
                    ->where('location_slug', $city)
                    ->where('is_published', true)
                    ->where('noindex', false)
                    ->count(),
                $city,
            );
        }

        LocalServiceLanding::query()
            ->where('is_published', true)
            ->where('noindex', false)
            ->get()
            ->each(function (LocalServiceLanding $landing): void {
                foreach (['en', 'ru'] as $locale) {
                    foreach (['title', 'content', 'seoTitle', 'seoDescription'] as $field) {
                        $this->assertNotEmpty(
                            data_get($landing->translations, "fields.{$field}.{$locale}"),
                            "{$landing->location_slug} {$field}.{$locale}",
                        );
                    }
                }
            });

        $computer = CategoryForService::query()
            ->where('slug', 'computer-services')
            ->sole();

        $this->assertSame(
            'კომპიუტერის გამართვა, აწყობა და პროგრამული მომსახურება',
            $computer->seo_title,
        );
        $this->assertSame(
            'Computer Setup, Assembly & Software Services',
            data_get($computer->translations, 'fields.seo_title.en'),
        );
    }

    public function test_canonical_local_seeder_is_idempotent(): void
    {
        $this->seed(ProductionContentSeeder::class);
        $count = LocalServiceLanding::query()->count();

        $this->seed(CanonicalLocalSeoSeeder::class);

        $this->assertDatabaseCount('local_service_landings', $count);
    }
}
