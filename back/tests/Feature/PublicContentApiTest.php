<?php

namespace Tests\Feature;

use App\Application\Content\PublicContentService;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_bootstrap_does_not_expose_bundled_page_copy(): void
    {
        $this->seed(SystemContentSeeder::class);

        $translations = collect(
            $this->getJson('/api/content')
                ->assertOk()
                ->json('data.settings.translations.entries'),
        )->keyBy('key');

        $this->assertNotEmpty($translations->get('nav.home'));
        $this->assertNotEmpty($translations->get('forms.email'));
        $this->assertNull($translations->get('home.hero.titlePrefix'));
        $this->assertNull($translations->get('home.infrastructure.title'));
        $this->assertNull($translations->get('about.hero.title'));
        $this->assertNull($translations->get('services.hero.titlePrefix'));
    }

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

        foreach (['team', 'partners', 'faqs'] as $key) {
            $this->assertIsArray($content[$key]);
            $this->assertTrue(array_is_list($content[$key]));
        }

        $this->getJson('/api/projects')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/projects/office-network-upgrade?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.name', 'Модернизация офисной сети')
            ->assertJsonPath('data.title', 'Полная модернизация офисной сети')
            ->assertJsonPath('data.categoryName', 'Офисы');
        $this->getJson('/api/content')->assertOk()->assertJsonStructure(['data' => ['team', 'partners', 'faqs', 'settings']]);
    }

    public function test_public_content_exposes_contact_phones_and_social_links_without_private_email(): void
    {
        $this->seed(ContentSeeder::class);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact'],
            [
                'group' => 'general',
                'is_public' => true,
                'value' => [
                    'phone' => '+995 555 00 11 22',
                    'phones' => [
                        ['label' => 'Sales', 'value' => '+995 555 00 11 22'],
                        ['label' => 'Support', 'value' => '+995 577 88 99 00'],
                    ],
                    'email' => 'hello@safetech.ge',
                    'address' => 'Tbilisi, Georgia',
                    'lead_email' => 'private@safetech.ge',
                ],
            ],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'socials'],
            [
                'group' => 'general',
                'is_public' => true,
                'value' => [
                    'links' => [
                        ['network' => 'facebook', 'href' => 'https://facebook.com/safetechge'],
                        ['network' => 'linkedin', 'href' => 'https://linkedin.com/company/safetech'],
                    ],
                    'share_title' => 'Share',
                    'share_buttons' => ['facebook', 'linkedin'],
                ],
            ],
        );

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.contact.phone', '+995 555 00 11 22')
            ->assertJsonPath('data.settings.contact.phones.0', '+995 555 00 11 22')
            ->assertJsonPath('data.settings.contact.phones.1', '+995 577 88 99 00')
            ->assertJsonPath('data.settings.contact.email', 'hello@safetech.ge')
            ->assertJsonPath('data.settings.socials.links.0.network', 'facebook')
            ->assertJsonPath('data.settings.socials.links.1.network', 'linkedin')
            ->assertJsonMissingPath('data.settings.contact.lead_email');
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

    public function test_missing_service_slug_returns_a_clean_not_found_response(): void
    {
        $this->seed(ContentSeeder::class);

        $this->getJson('/api/services/no-such-service')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Service not found.',
            ]);
    }

    public function test_missing_project_slug_returns_a_clean_not_found_response(): void
    {
        $this->seed(ContentSeeder::class);

        $this->getJson('/api/projects/no-such-project')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Project not found.',
            ]);
    }

    public function test_public_content_exposes_shop_feature_flag_only_when_products_exist(): void
    {
        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.features.shop_enabled', false);

        $category = ProductCategory::query()->create([
            'name' => 'Shop',
            'slug' => 'shop',
        ]);
        Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Visible product',
            'slug' => 'visible-product',
            'short_description' => 'Short product description',
            'description' => 'Long product description',
            'is_published' => true,
        ]);

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.features.shop_enabled', true);
    }

    public function test_public_content_gracefully_disables_shop_when_product_tables_are_missing(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_categories');

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('data.settings.features.shop_enabled', false);
    }
}
