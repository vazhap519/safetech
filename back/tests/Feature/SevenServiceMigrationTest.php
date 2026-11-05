<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteCatalogItem;
use App\Models\Service;
use Database\Seeders\GoogleBusinessServiceDefinitions;
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

    public function test_execute_preserves_complete_content_snapshots_and_is_idempotent(): void
    {
        $this->seed(ProductionContentSeeder::class);
        $canonical = Service::query()->where('slug', 'security-camera-installation')->sole();
        $legacy = $canonical->replicate();
        $legacy->slug = 'ip-camera-installation';
        $legacy->save();

        $category = ProjectCategory::query()->create([
            'slug' => 'customer-projects', 'name' => 'Customer projects', 'sort_order' => 1,
        ]);
        for ($index = 1; $index <= 17; $index++) {
            Project::query()->create([
                'slug' => "customer-project-{$index}",
                'name' => "Customer project {$index}",
                'title' => "Customer project {$index}",
                'description' => 'Administrator content to preserve',
                'seo_description' => 'Original customer project SEO',
                'category_id' => $category->id,
                'is_published' => true,
            ]);
        }

        // Include a real media record with historical service ownership.
        DB::table('media')->insert([
            'model_type' => Service::class,
            'model_id' => $legacy->id,
            'collection_name' => 'images',
            'name' => 'Customer photo',
            'file_name' => 'customer-photo.jpg',
            'mime_type' => 'image/jpeg',
            'disk' => 'public',
            'size' => 1234,
            'manipulations' => '{}',
            'custom_properties' => '{}',
            'generated_conversions' => '{}',
            'responsive_images' => '{}',
        ]);

        // Reconstruct 204 historical local rows without copying production data.
        foreach (GoogleBusinessServiceDefinitions::CANONICAL_ALIASES as $slug => $targetSlug) {
            $target = Service::query()->where('slug', $targetSlug)->sole();
            $source = Service::query()->where('slug', $slug)->first();
            if (! $source) {
                $source = $target->replicate();
                $source->slug = $slug;
                $source->save();
            }
            foreach (LocalServiceLanding::query()->where('service_id', $target->id)->get() as $landing) {
                if (DB::table('local_service_landings')->count() >= 204) {
                    break 2;
                }
                $copy = $landing->replicate();
                $copy->service_id = $source->id;
                $copy->save();
            }
        }

        $tables = ['projects', 'media', 'quote_catalog_items', 'local_service_landings'];
        $before = [];
        foreach ($tables as $table) {
            $before[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        $this->assertSame(204, DB::table('local_service_landings')->count());
        $this->assertGreaterThan(0, DB::table('quote_catalog_items')->count());

        for ($run = 0; $run < 2; $run++) {
            $this->artisan('safetech:seven-service-preflight', ['--execute' => true])->assertExitCode(0);
            foreach ($tables as $table) {
                $this->assertSame($before[$table], DB::table($table)->orderBy('id')->get()->toJson(), $table);
            }
            $this->assertSame(7, Service::query()->where('is_published', true)->count());
            $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
        }
    }
}
