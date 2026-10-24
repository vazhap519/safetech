<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteCatalogItem;
use App\Models\Service;
use Database\Seeders\ProductionContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SevenServiceMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_keeps_legacy_quotes_seo_media_ownership_and_merges_project_links(): void
    {
        $this->seed(ProductionContentSeeder::class);

        $canonical = Service::query()->where('slug', 'security-camera-installation')->sole();
        $target = LocalServiceLanding::query()
            ->where('service_id', $canonical->id)
            ->where('location_slug', 'tbilisi')
            ->sole();

        $legacy = $canonical->replicate();
        $legacy->slug = 'ip-camera-installation';
        $legacy->save();

        $source = $target->replicate();
        $source->service_id = $legacy->id;
        $source->title = 'Preserve legacy localized content';
        $source->content = 'Preserve legacy SEO copy';
        $source->save();

        $category = ProjectCategory::query()->create([
            'slug' => 'migration-test-category',
            'name' => 'Migration test',
            'sort_order' => 1,
        ]);
        $project = Project::query()->create([
            'slug' => 'migration-test-project',
            'name' => 'Migration test',
            'title' => 'Migration test',
            'description' => 'Test project',
            'seo_description' => 'Test project',
            'category_id' => $category->id,
            'is_published' => true,
        ]);
        $source->projects()->attach($project);

        $quote = QuoteCatalogItem::query()->create([
            'service_id' => $legacy->id,
            'component_key' => 'device-service',
            'name' => 'Legacy quote',
            'category' => 'other',
            'purchase_price' => 123,
            'sale_price' => 250,
            'is_active' => true,
        ]);

        $this->artisan('safetech:seven-service-preflight', ['--execute' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('services', ['id' => $legacy->id, 'is_published' => false]);
        $this->assertDatabaseHas('local_service_landings', [
            'id' => $source->id,
            'service_id' => $legacy->id,
            'content' => 'Preserve legacy SEO copy',
        ]);
        $this->assertDatabaseHas('local_service_landing_project', [
            'landing_id' => $target->id,
            'project_id' => $project->id,
        ]);
        $this->assertDatabaseHas('quote_catalog_items', [
            'id' => $quote->id,
            'service_id' => $legacy->id,
            'purchase_price' => 123,
            'sale_price' => 250,
        ]);

        $this->artisan('safetech:seven-service-preflight', ['--execute' => true])
            ->assertExitCode(0);

        $this->assertSame(1, DB::table('local_service_landing_project')
            ->where('landing_id', $target->id)
            ->where('project_id', $project->id)
            ->count());
    }

    public function test_execute_creates_missing_seventh_canonical_service(): void
    {
        $this->seed(ProductionContentSeeder::class);

        Service::query()->where('slug', 'network-installation-configuration')->sole()->delete();

        $this->assertDatabaseMissing('services', ['slug' => 'network-installation-configuration']);

        $this->artisan('safetech:seven-service-preflight', ['--execute' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('services', ['slug' => 'network-installation-configuration']);
    }

    public function test_execute_preserves_all_quote_rows_with_colliding_component_keys(): void
    {
        $this->seed(ProductionContentSeeder::class);

        $canonical = Service::query()->where('slug', 'security-camera-installation')->sole();
        $first = $canonical->replicate();
        $first->slug = 'ip-camera-installation';
        $first->save();

        $second = $canonical->replicate();
        $second->slug = 'video-surveillance-system-installation';
        $second->save();

        foreach ([$canonical, $first, $second] as $index => $service) {
            QuoteCatalogItem::query()->create([
                'service_id' => $service->id,
                'component_key' => 'migration-conflicting-component',
                'name' => 'Preserved collision test',
                'category' => 'other',
                'purchase_price' => 100 + $index,
                'sale_price' => 160 + $index,
                'is_active' => true,
            ]);
        }

        $before = DB::table('quote_catalog_items')->count();

        $this->artisan('safetech:seven-service-preflight', ['--execute' => true])
            ->assertExitCode(0);

        $this->assertSame($before, DB::table('quote_catalog_items')->count());
        $this->assertSame(3, DB::table('quote_catalog_items')
            ->where('component_key', 'migration-conflicting-component')
            ->count());

        $this->artisan('safetech:seven-service-preflight', ['--execute' => true])
            ->assertExitCode(0);

        $this->assertSame($before, DB::table('quote_catalog_items')->count());
    }
}
