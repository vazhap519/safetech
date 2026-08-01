<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCalculatorProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_default_profiles_and_component_catalog_when_the_service_catalog_is_empty(): void
    {
        $this->getJson('/api/service-calculator/profiles?locale=en')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.slug', 'cctv')
            ->assertJsonPath('data.0.name', 'CCTV')
            ->assertJsonPath('data.0.discountPercentage', 0)
            ->assertJsonPath('data.0.fields.0.key', 'camera_technology')
            ->assertJsonFragment([
                'key' => 'nvr-16ch-poe',
                'category' => 'recorder',
                'unitPrice' => 1040,
                'quantityMode' => 'ceil',
                'quantityField' => 'camera_count',
                'unitsPerComponent' => 16,
                'required' => true,
            ]);

        $this->getJson('/api/service-calculator/profiles?locale=en&service=networking')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'networking')
            ->assertJsonPath('data.0.name', 'Networking');
    }

    public function test_it_returns_admin_managed_prices_discount_fields_and_components(): void
    {
        Service::query()->create([
            'slug' => 'networking',
            'name' => 'ქსელი',
            'title' => 'ქსელი',
            'description' => 'ქსელის მოწყობა',
            'seo_description' => 'ქსელის მოწყობა',
            'is_published' => true,
            'translations' => [
                'fields' => [
                    'name' => ['ka' => 'ქსელი', 'en' => 'Networking', 'ru' => 'Сеть'],
                    'description' => ['ka' => 'ქსელის მოწყობა', 'en' => 'Network setup', 'ru' => 'Монтаж сети'],
                ],
            ],
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => [
                    'currency' => 'GEL',
                    'base_price' => 250,
                    'labor_price' => 180,
                    'discount_percentage' => 12.5,
                ],
                'project_size_label_en' => 'Network size',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა', 'en' => 'Small', 'one_time_price' => 100],
                ],
                'extra_fields' => [
                    [
                        'key' => 'network_points',
                        'type' => 'number',
                        'ka' => 'წერტილები',
                        'en' => 'Network points',
                        'unit_en' => 'points',
                        'unit_price' => 2.5,
                    ],
                ],
                'components' => [
                    [
                        'key' => 'switch-24',
                        'category' => 'network',
                        'title_ka' => '24-პორტიანი სვიჩი',
                        'title_en' => '24-port switch',
                        'unit_price' => 450,
                        'quantity_mode' => 'ceil',
                        'quantity_field' => 'network_points',
                        'units_per_component' => 24,
                        'required' => true,
                        'recommended' => true,
                        'exclusive_group' => 'switch',
                        'priority' => 100,
                        'rules' => [
                            ['field' => 'network_points', 'operator' => 'gte', 'value' => '1'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->getJson('/api/service-calculator/profiles?locale=en&service=networking')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Networking')
            ->assertJsonPath('data.0.basePrice', 250)
            ->assertJsonPath('data.0.laborPrice', 180)
            ->assertJsonPath('data.0.discountPercentage', 12.5)
            ->assertJsonPath('data.0.projectSize.options.0.label', 'Small')
            ->assertJsonPath('data.0.fields.0.key', 'network_points')
            ->assertJsonPath('data.0.fields.0.unitPrice', 2.5)
            ->assertJsonPath('data.0.components.0.key', 'switch-24')
            ->assertJsonPath('data.0.components.0.title', '24-port switch')
            ->assertJsonPath('data.0.components.0.rules.0.operator', 'gte');
    }

    public function test_it_supplies_a_compatibility_aware_default_profile_for_a_published_service_without_settings(): void
    {
        Service::query()->create([
            'slug' => 'cctv-installation',
            'name' => 'კამერების მონტაჟი',
            'title' => 'კამერების მონტაჟი',
            'description' => 'ვიდეოსამეთვალყურეობის სისტემის მონტაჟი',
            'seo_description' => 'ვიდეოსამეთვალყურეობის სისტემის მონტაჟი',
            'is_published' => true,
            'lead_form' => [],
        ]);

        $this->getJson('/api/service-calculator/profiles?locale=ka&service=cctv-installation')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'cctv-installation')
            ->assertJsonPath('data.0.fields.0.key', 'camera_technology')
            ->assertJsonPath('data.0.fields.1.key', 'camera_count')
            ->assertJsonPath('data.0.currency', 'GEL')
            ->assertJsonFragment([
                'key' => 'camera-ip-4mp-2-8',
                'unitPrice' => 176,
                'quantityMode' => 'field',
                'quantityField' => 'camera_count',
            ])
            ->assertJsonFragment([
                'field' => 'resolution',
                'operator' => 'equals',
                'value' => '4mp',
            ]);
    }

    public function test_it_hides_disabled_and_unpublished_profiles(): void
    {
        foreach ([
            ['slug' => 'disabled', 'is_published' => true, 'lead_form' => ['calculator_enabled' => false]],
            ['slug' => 'draft', 'is_published' => false, 'lead_form' => ['calculator_enabled' => true]],
        ] as $service) {
            Service::query()->create(array_merge([
                'name' => $service['slug'],
                'title' => $service['slug'],
                'description' => 'Description',
                'seo_description' => 'SEO description',
            ], $service));
        }

        $this->getJson('/api/service-calculator/profiles')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
