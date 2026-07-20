<?php

namespace Tests\Feature;

use App\Application\Content\PublicContentService;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_published_services(): void
    {
        $this->seed(ContentSeeder::class);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'cctv')
            ->assertJsonStructure(['data' => [['slug', 'name', 'title', 'description', 'faqs']]]);
    }

    public function test_it_returns_a_service_by_slug(): void
    {
        $this->seed(ContentSeeder::class);

        $this->getJson('/api/services/networking')
            ->assertOk()
            ->assertJsonPath('data.slug', 'networking');
    }

    public function test_it_returns_projects_and_shared_content(): void
    {
        $this->seed(ContentSeeder::class);

        $content = $this->app->make(PublicContentService::class)->bootstrap();

        foreach (['team', 'partners', 'testimonials', 'faqs'] as $key) {
            $this->assertIsArray($content[$key]);
            $this->assertTrue(array_is_list($content[$key]));
        }

        $this->getJson('/api/projects')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/content')->assertOk()->assertJsonStructure(['data' => ['team', 'partners', 'testimonials', 'faqs', 'settings']]);
    }
}
