<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceLocalizedFieldsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_detail_localizes_repeaters_lists_warranty_and_sla(): void
    {
        Service::query()->create([
            'slug' => 'network-installation',
            'name' => 'ქსელის მონტაჟი',
            'title' => 'ქსელის მონტაჟი',
            'description' => 'ქართული აღწერა',
            'keywords' => ['ქსელი'],
            'highlights' => ['სწორი დაგეგმვა'],
            'industries' => ['ოფისები'],
            'benefits' => [[
                'title' => 'სტაბილური ქსელი',
                'description' => 'ქართული სარგებელი',
                'translations' => [
                    'en' => ['title' => 'Stable network', 'description' => 'English benefit'],
                    'ru' => ['title' => 'Стабильная сеть', 'description' => 'Русское преимущество'],
                ],
            ]],
            'warranty' => 'ქართული გარანტია',
            'sla' => 'ქართული SLA',
            'translations' => [
                'keywords' => ['en' => ['network'], 'ru' => ['сеть']],
                'highlights' => ['en' => ['Correct planning'], 'ru' => ['Правильное планирование']],
                'industries' => ['en' => ['Offices'], 'ru' => ['Офисы']],
                'fields' => [
                    'warranty' => ['en' => 'English warranty', 'ru' => 'Русская гарантия'],
                    'sla' => ['en' => 'English SLA', 'ru' => 'Русский SLA'],
                    'ogTitle' => ['en' => 'Network installation preview', 'ru' => 'Монтаж сети'],
                    'ogDescription' => ['en' => 'English Open Graph description.', 'ru' => 'Описание Open Graph.'],
                ],
            ],
            'seo' => [
                'canonical' => 'https://safetech.ge/services/network-installation',
                'schema_type' => 'Service',
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/services/network-installation?locale=en')
            ->assertOk()
            ->assertJsonPath('data.keywords.0', 'network')
            ->assertJsonPath('data.highlights.0', 'Correct planning')
            ->assertJsonPath('data.industries.0', 'Offices')
            ->assertJsonPath('data.benefits.0.title', 'Stable network')
            ->assertJsonPath('data.warranty', 'English warranty')
            ->assertJsonPath('data.sla', 'English SLA')
            ->assertJsonPath('data.seo.og.title', 'Network installation preview')
            ->assertJsonPath('data.seo.canonical', 'https://safetech.ge/services/network-installation')
            ->assertJsonPath('data.seo.schemaType', 'Service');

        $this->getJson('/api/services/network-installation?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.benefits.0.description', 'Русское преимущество')
            ->assertJsonPath('data.warranty', 'Русская гарантия');
    }
}
