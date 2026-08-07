<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOptionsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_published_services_as_lightweight_options(): void
    {
        Service::query()->create([
            'slug' => 'networking',
            'name' => 'ქსელის მოწყობა',
            'title' => 'ქსელის მოწყობა',
            'description' => 'ქსელის მოწყობის აღწერა',
            'seo_description' => 'ქსელის მოწყობის SEO აღწერა',
            'lead_form' => [
                'extra_fields' => [
                    ['key' => 'router_count', 'type' => 'number'],
                ],
            ],
            'is_published' => true,
        ]);

        Service::query()->create([
            'slug' => 'draft-service',
            'name' => 'Draft service',
            'title' => 'Draft service',
            'description' => 'Draft description',
            'seo_description' => 'Draft SEO description',
            'is_published' => false,
        ]);

        $response = $this->getJson('/api/services/options?locale=ka')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'networking')
            ->assertJsonPath('data.0.label', 'ქსელის მოწყობა');

        $this->assertArrayNotHasKey('leadForm', $response->json('data.0'));
        $this->assertArrayNotHasKey('description', $response->json('data.0'));
    }
}
