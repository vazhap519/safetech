<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteCatalogItem;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ProductionContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshPublicContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_destructive_refresh_is_blocked_until_seven_service_migration_is_verified(): void
    {
        $this->seed(ProductionContentSeeder::class);

        $canonical = Service::query()
            ->where('slug', 'security-camera-installation')
            ->sole();

        $alias = $canonical->replicate();
        $alias->slug = 'ip-camera-installation';
        $alias->name = 'Legacy IP camera service';
        $alias->title = 'Legacy IP camera service';
        $alias->save();

        SiteSetting::query()->updateOrCreate(
            ['key' => 'system_content_seed_version'],
            [
                'group' => 'system',
                'is_public' => false,
                'value' => [
                    'version' => '2026-09-30-canonical-seo-v4',
                    'applied_at' => now()->subDay()->toAtomString(),
                ],
            ],
        );

        $this->artisan('safetech:refresh-public-content', ['--force' => true])
            ->assertExitCode(1);

        $this->assertDatabaseHas('services', ['slug' => 'ip-camera-installation']);
        $this->assertSame('2026-09-30-canonical-seo-v4', data_get(SiteSetting::query()->where('key', 'system_content_seed_version')->sole()->value, 'version'));
    }

    public function test_refresh_rebuilds_public_seo_and_preserves_projects_categories_and_quote_data(): void
    {
        $this->seed(ProductionContentSeeder::class);

        $projectCategory = ProjectCategory::query()->create([
            'slug' => 'real-project-category',
            'name' => 'რეალური პროექტების კატეგორია',
            'sort_order' => 50,
        ]);
        $project = Project::query()->create([
            'slug' => 'real-project',
            'name' => 'რეალური პროექტი',
            'title' => 'რეალური პროექტი',
            'description' => 'რეალური შესრულებული პროექტი.',
            'seo_description' => 'რეალური შესრულებული პროექტი.',
            'excerpt' => 'რეალური პროექტი.',
            'content' => 'რეალური შესრულებული პროექტის სრული აღწერა.',
            'category_id' => $projectCategory->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $canonical = Service::query()
            ->where('slug', 'security-camera-installation')
            ->sole();
        $landing = LocalServiceLanding::query()
            ->where('service_id', $canonical->id)
            ->where('location_slug', 'tbilisi')
            ->sole();
        $landing->projects()->attach($project);

        $alias = $canonical->replicate();
        $alias->slug = 'ip-camera-installation';
        $alias->name = 'ძველი IP კამერების გვერდი';
        $alias->title = 'ძველი IP კამერების გვერდი';
        $alias->save();

        QuoteCatalogItem::query()->create([
            'service_id' => $alias->id,
            'component_key' => 'legacy-camera',
            'name' => 'Legacy camera price',
            'category' => 'camera',
            'purchase_price' => 75,
            'markup_percentage' => 60,
            'is_active' => true,
        ]);

        $contact = SiteSetting::query()->where('key', 'contact')->sole();
        $contactValue = $contact->value;
        $contactValue['service_area_business'] = false;
        $contactValue['address'] = 'თბილისი, ძველი მისამართი';
        $contactValue['address_en'] = 'Old Tbilisi address';
        $contactValue['address_ru'] = 'Старый адрес';
        $contact->value = $contactValue;
        $contact->save();

        $this->artisan('safetech:refresh-public-content', ['--force' => true])
            ->assertExitCode(1);

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('project_categories', ['id' => $projectCategory->id]);
        $this->assertDatabaseHas('services', ['slug' => 'ip-camera-installation']);
        $this->assertDatabaseHas('quote_catalog_items', ['service_id' => $alias->id, 'component_key' => 'legacy-camera']);
        $this->assertDatabaseHas('local_service_landing_project', ['landing_id' => $landing->id, 'project_id' => $project->id]);
    }
}
