<?php

namespace Tests\Feature;

use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seed_data_contains_calculators_translations_and_static_seo(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(5, Service::query()->published()->count());
        $this->assertSame(5, Service::query()
            ->whereNotNull('lead_form')
            ->get()
            ->filter(fn (Service $service): bool => data_get($service->lead_form, 'calculator_enabled') === true)
            ->count());
        $this->assertTrue(SeoPage::query()->where('key', 'service-calculator')->exists());
        $this->assertNotEmpty(data_get(
            SiteSetting::query()->where('key', 'translations')->value('value'),
            'entries',
        ));
    }
}
