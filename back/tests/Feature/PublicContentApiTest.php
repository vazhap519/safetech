<?php

namespace Tests\Feature;

use App\Application\Content\PublicContentService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
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

        $this->getJson('/api/services/cctv?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.name', 'Видеонаблюдение')
            ->assertJsonPath('data.title', 'Монтаж и мониторинг видеонаблюдения')
            ->assertJsonPath('data.category.name', 'Системы безопасности');
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
        $this->getJson('/api/projects/office-network-upgrade?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.name', 'Модернизация офисной сети')
            ->assertJsonPath('data.title', 'Полная модернизация офисной сети')
            ->assertJsonPath('data.categoryName', 'Офисы');
        $this->getJson('/api/content')->assertOk()->assertJsonStructure(['data' => ['team', 'partners', 'testimonials', 'faqs', 'settings']]);
    }

    public function test_filament_custom_entries_are_available_to_the_frontend_in_all_locales(): void
    {
        $this->seed(ContentSeeder::class);

        $service = Service::query()->where('slug', 'cctv')->firstOrFail();
        $translations = $service->translations;
        $translations['entries'] = [
            [
                'key' => 'benefit.0.title',
                'ka' => 'ქართული სარგებელი',
                'en' => 'English benefit',
                'ru' => 'Русское преимущество',
            ],
        ];
        $service->forceFill(['translations' => $translations])->save();

        $entries = collect(
            $this->getJson('/api/content')
                ->assertOk()
                ->json('data.settings.translations.entries'),
        )->keyBy('key');

        $this->assertSame(
            [
                'key' => 'service.cctv.benefit.0.title',
                'ka' => 'ქართული სარგებელი',
                'en' => 'English benefit',
                'ru' => 'Русское преимущество',
            ],
            $entries->get('service.cctv.benefit.0.title'),
        );
    }

    public function test_project_details_only_link_to_currently_published_related_projects(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Offices',
            'slug' => 'offices',
        ]);
        $hidden = Project::query()->create([
            'category_id' => $category->id,
            'slug' => 'hidden-project',
            'name' => 'Hidden project',
            'title' => 'Hidden project',
            'description' => 'This project is not public.',
            'is_published' => false,
        ]);
        $related = Project::query()->create([
            'category_id' => $category->id,
            'slug' => 'published-related-project',
            'name' => 'Published related project',
            'title' => 'Published related project',
            'description' => 'A public related project.',
            'image_alt' => 'Related project image',
            'is_published' => true,
        ]);
        $project = Project::query()->create([
            'category_id' => $category->id,
            'slug' => 'main-project',
            'name' => 'Main project',
            'title' => 'Main project',
            'description' => 'The main public project.',
            'related' => [
                ['slug' => $hidden->slug],
                ['slug' => $related->slug],
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonPath('data.0.related', []);

        $this->getJson("/api/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonCount(1, 'data.related')
            ->assertJsonPath('data.related.0.slug', $related->slug)
            ->assertJsonPath('data.related.0.title', $related->title)
            ->assertJsonPath('data.related.0.translationIndex', 1);
    }
}
