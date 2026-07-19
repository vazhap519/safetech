<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCalculatorProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_localized_dynamic_calculator_profile(): void
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
                'pricing' => ['currency' => 'GEL', 'base_price' => 250],
                'project_size_label_en' => 'Network size',
                'project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა', 'en' => 'Small', 'one_time_price' => 100],
                ],
                'extra_fields' => [
                    [
                        'key' => 'cable_meters',
                        'type' => 'number',
                        'ka' => 'კაბელი',
                        'en' => 'Cable length',
                        'unit_en' => 'm',
                        'unit_price' => 2.5,
                        'price_multiplier_field' => 'rooms',
                    ],
                ],
            ],
        ]);

        $this->getJson('/api/service-calculator/profiles?locale=en&service=networking')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Networking')
            ->assertJsonPath('data.0.basePrice', 250)
            ->assertJsonPath('data.0.projectSize.options.0.label', 'Small')
            ->assertJsonPath('data.0.fields.0.key', 'cable_meters')
            ->assertJsonPath('data.0.fields.0.priceMultiplierField', 'rooms')
            ->assertJsonPath('data.0.fields.0.unitPrice', 2.5);
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
