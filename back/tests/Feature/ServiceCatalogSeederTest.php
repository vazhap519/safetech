<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_complete_multilingual_it_service_catalog_idempotently(): void
    {
        $this->seed(ServiceCatalogSeeder::class);
        $this->seed(ServiceCatalogSeeder::class);

        $this->assertDatabaseCount('category_for_services', 4);
        $this->assertDatabaseCount('services', 12);
        $this->assertDatabaseCount('faqs', 36);

        $service = Service::query()
            ->with(['category', 'faqs'])
            ->where('slug', 'security-camera-installation')
            ->firstOrFail();

        $this->assertTrue($service->is_published);
        $this->assertSame('security-access-automation', $service->category?->slug);
        $this->assertSame('Security Camera Installation and Setup', data_get($service->translations, 'fields.name.en'));
        $this->assertSame('Монтаж и настройка камер видеонаблюдения', data_get($service->translations, 'fields.name.ru'));
        $this->assertNotEmpty(data_get($service->translations, 'fields.seoTitle.en'));
        $this->assertNotEmpty(data_get($service->translations, 'fields.seoDescription.ru'));
        $this->assertTrue((bool) data_get($service->lead_form, 'calculator_enabled'));
        $this->assertCount(3, $service->faqs);
        $this->assertNotEmpty(data_get($service->faqs->first()?->translations, 'fields.question.en'));

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translationMap = MultilingualContent::mapFrom($translations->value);

        $this->assertSame(
            'CCTV Camera Installation in Georgia | SafeTech',
            data_get($translationMap, 'service.security-camera-installation.seoTitle.en'),
        );
        $this->assertNotEmpty(
            data_get($translationMap, 'service.router-wifi-configuration.seoDescription.ru'),
        );
    }

    public function test_it_covers_every_requested_service_and_assigns_each_to_a_category(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $expectedSlugs = [
            'operating-system-installation',
            'custom-computer-build',
            'computer-cleaning-maintenance',
            'rack-assembly-cable-management',
            'pos-system-installation',
            'business-it-support',
            'security-camera-installation',
            'intercom-access-control-installation',
            'router-wifi-configuration',
            'network-cable-installation',
            'patch-panel-network-outlet-installation',
            'barrier-gate-installation',
        ];

        $services = Service::query()->whereIn('slug', $expectedSlugs)->get();

        $this->assertCount(count($expectedSlugs), $services);
        $this->assertTrue($services->every(fn (Service $service): bool => $service->category_for_service_id !== null));
        $this->assertTrue($services->every(fn (Service $service): bool => filled(data_get($service->seo, 'title'))));
        $this->assertTrue($services->every(fn (Service $service): bool => filled($service->seo_description)));
        $this->assertTrue($services->every(fn (Service $service): bool => is_array($service->keywords) && count($service->keywords) >= 3));

        $this->assertSame(4, CategoryForService::query()->count());
        $this->assertSame(36, Faq::query()->count());
    }
}
