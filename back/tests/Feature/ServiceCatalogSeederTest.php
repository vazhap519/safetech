<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\MultilingualContent;
use Database\Seeders\GoogleBusinessServiceDefinitions;
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

        $this->assertDatabaseCount('category_for_services', 5);
        $this->assertDatabaseCount('services', 57);
        $this->assertDatabaseCount('faqs', 136);

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
            $translationMap['service.security-camera-installation.seoTitle']['en'] ?? null,
        );
        $this->assertSame(
            'Drivers and updates',
            $translationMap['service.operating-system-installation.highlight.1']['en'] ?? null,
        );
        $this->assertSame(
            'VLAN, VPN and firewall',
            $translationMap['service.router-wifi-configuration.highlight.2']['en'] ?? null,
        );
        $this->assertSame(
            'VLAN, VPN и межсетевой экран',
            $translationMap['service.router-wifi-configuration.benefit.2.title']['ru'] ?? null,
        );
        $this->assertSame(
            'Настройка драйверов и программ',
            $translationMap['service.operating-system-installation.solution.1.title']['ru'] ?? null,
        );
        $this->assertNotEmpty(
            $translationMap['service.router-wifi-configuration.seoDescription']['ru'] ?? null,
        );
    }

    public function test_it_replaces_legacy_georgian_router_highlight_placeholders(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'translations'],
            [
                'group' => 'general',
                'is_public' => true,
                'value' => [
                    'entries' => [
                        [
                            'key' => 'service.router-wifi-configuration.highlight.2',
                            'ka' => 'VLAN, VPN და Firewall',
                            'en' => 'VLAN, VPN და Firewall',
                            'ru' => 'VLAN, VPN და Firewall',
                        ],
                        [
                            'key' => 'service.router-wifi-configuration.benefit.2.title',
                            'ka' => 'VLAN, VPN და Firewall',
                            'en' => 'VLAN, VPN და Firewall',
                            'ru' => 'VLAN, VPN და Firewall',
                        ],
                    ],
                ],
            ],
        );

        $this->seed(ServiceCatalogSeeder::class);

        $translations = SiteSetting::query()->where('key', 'translations')->firstOrFail();
        $translationMap = MultilingualContent::mapFrom($translations->value);

        $this->assertSame(
            'VLAN, VPN and firewall',
            $translationMap['service.router-wifi-configuration.highlight.2']['en'] ?? null,
        );
        $this->assertSame(
            'VLAN, VPN и межсетевой экран',
            $translationMap['service.router-wifi-configuration.highlight.2']['ru'] ?? null,
        );
        $this->assertSame(
            'VLAN, VPN and firewall',
            $translationMap['service.router-wifi-configuration.benefit.2.title']['en'] ?? null,
        );
        $this->assertSame(
            'VLAN, VPN и межсетевой экран',
            $translationMap['service.router-wifi-configuration.benefit.2.title']['ru'] ?? null,
        );
    }

    public function test_it_covers_every_requested_service_and_assigns_each_to_a_category(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $coreSlugs = [
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
        $googleBusinessSlugs = array_column(
            GoogleBusinessServiceDefinitions::all(),
            'slug',
        );
        $expectedSlugs = [...$coreSlugs, ...$googleBusinessSlugs];

        $services = Service::query()->whereIn('slug', $expectedSlugs)->get();

        $this->assertCount(count($expectedSlugs), $services);
        $this->assertTrue($services->every(fn (Service $service): bool => $service->category_for_service_id !== null));
        $this->assertTrue($services->every(fn (Service $service): bool => filled(data_get($service->seo, 'title'))));
        $this->assertTrue($services->every(fn (Service $service): bool => filled($service->seo_description)));
        $this->assertTrue($services->every(fn (Service $service): bool => is_array($service->keywords) && count($service->keywords) >= 3));

        $this->assertSame(5, CategoryForService::query()->count());
        $this->assertSame(136, Faq::query()->count());

        $itSupport = Service::query()->where('slug', 'business-it-support')->firstOrFail();
        $this->assertSame(
            'IT მხარდაჭერა და IT მომსახურება ბიზნესისთვის | SafeTech',
            data_get($itSupport->seo, 'title'),
        );
        $this->assertSame(
            'Business IT Support and IT Services | SafeTech',
            data_get($itSupport->translations, 'fields.seoTitle.en'),
        );

        $barrier = Service::query()->with('faqs')->where('slug', 'barrier-gate-installation')->firstOrFail();
        $this->assertSame(
            'შლაგბაუმის მონტაჟი — LPR, GSM და ავტომატური მართვა | SafeTech',
            data_get($barrier->seo, 'title'),
        );
        $this->assertCount(7, $barrier->faqs);

        $businessIt = CategoryForService::query()->where('slug', 'business-it')->firstOrFail();
        $this->assertSame('ბიზნეს IT სისტემები და IT ინფრასტრუქტურა', $businessIt->seo_title);
    }

    public function test_google_business_profile_services_are_fully_localized_in_all_three_languages(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $expected = collect(GoogleBusinessServiceDefinitions::all())->keyBy('slug');
        $services = Service::query()
            ->whereIn('slug', $expected->keys())
            ->get()
            ->keyBy('slug');

        $this->assertCount($expected->count(), $services);

        foreach ($expected as $slug => $definition) {
            $service = $services->get($slug);

            $this->assertNotNull($service, "Missing Google Business service: {$slug}");

            foreach (['ka', 'en', 'ru'] as $locale) {
                $this->assertSame(
                    $definition['name'][$locale],
                    data_get($service->translations, "fields.name.{$locale}"),
                    "{$slug} is missing the {$locale} name.",
                );
                $this->assertNotEmpty(
                    data_get($service->translations, "fields.description.{$locale}"),
                    "{$slug} is missing the {$locale} description.",
                );
                $this->assertNotEmpty(
                    data_get($service->translations, "fields.seoTitle.{$locale}"),
                    "{$slug} is missing the {$locale} SEO title.",
                );
                $this->assertNotEmpty(
                    data_get($service->translations, "fields.seoDescription.{$locale}"),
                    "{$slug} is missing the {$locale} SEO description.",
                );
            }
        }

        $telecommunications = CategoryForService::query()
            ->where('slug', 'telecommunications-infrastructure')
            ->firstOrFail();

        $this->assertSame(
            'Telecommunications Infrastructure',
            data_get($telecommunications->translations, 'fields.name.en'),
        );
        $this->assertSame(
            'Телекоммуникационная инфраструктура',
            data_get($telecommunications->translations, 'fields.name.ru'),
        );
    }

    public function test_google_business_services_and_categories_are_localized_by_the_public_api(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $this->getJson('/api/services/ip-camera-installation?locale=en')
            ->assertOk()
            ->assertJsonPath('data.name', 'IP Camera Installation')
            ->assertJsonPath('data.category.name', 'Security and Access Automation');

        $this->getJson('/api/services/ip-camera-installation?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.name', 'Монтаж IP-камер')
            ->assertJsonPath('data.category.name', 'Безопасность и автоматизация доступа');

        $this->getJson('/api/service-categories?locale=en')
            ->assertOk()
            ->assertJsonFragment([
                'slug' => 'telecommunications-infrastructure',
                'name' => 'Telecommunications Infrastructure',
            ]);

        $this->getJson('/api/service-categories?locale=ru')
            ->assertOk()
            ->assertJsonFragment([
                'slug' => 'telecommunications-infrastructure',
                'name' => 'Телекоммуникационная инфраструктура',
            ]);
    }
}
