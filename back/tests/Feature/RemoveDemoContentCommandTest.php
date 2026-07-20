<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\PrivacyPolicy;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveDemoContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_only_bundled_demo_catalog_records(): void
    {
        config()->set('app.seed_demo_content', true);
        $this->seed(ContentSeeder::class);

        $customService = Service::query()->where('slug', 'cctv')->firstOrFail()->replicate();
        $customService->forceFill([
            'slug' => 'custom-security-service',
            'name' => 'Custom security service',
            'title' => 'Custom security service',
        ])->save();

        $customProject = Project::query()->where('slug', 'office-network-upgrade')->firstOrFail()->replicate();
        $customProject->forceFill([
            'slug' => 'custom-office-project',
            'name' => 'Custom office project',
            'title' => 'Custom office project',
        ])->save();

        $this->artisan('cms:remove-demo-content', ['--force' => true])
            ->assertSuccessful();

        foreach (['cctv', 'networking', 'access-control', 'server-infrastructure', 'it-support'] as $slug) {
            $this->assertDatabaseMissing('services', ['slug' => $slug]);
        }

        $this->assertDatabaseMissing('projects', ['slug' => 'office-network-upgrade']);
        $this->assertDatabaseHas('services', ['slug' => 'custom-security-service']);
        $this->assertDatabaseHas('projects', ['slug' => 'custom-office-project']);
        $this->assertDatabaseHas('category_for_services', ['slug' => 'security-systems']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'network-infrastructure']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'server-infrastructure']);
        $this->assertDatabaseMissing('category_for_services', ['slug' => 'it-support']);
        $this->assertDatabaseHas('project_categories', ['slug' => 'offices']);
        $this->assertDatabaseHas('site_settings', ['key' => 'translations']);
    }

    public function test_content_seeder_keeps_system_defaults_but_skips_demo_catalog_in_production(): void
    {
        config()->set('app.seed_demo_content', false);

        $this->seed(ContentSeeder::class);

        $this->assertSame(0, Service::query()->count());
        $this->assertSame(0, Project::query()->count());
        $this->assertSame(0, CategoryForService::query()->count());
        $this->assertSame(0, ProjectCategory::query()->count());
        $this->assertTrue(SiteSetting::query()->where('key', 'translations')->exists());
        $this->assertSame(1, PrivacyPolicy::query()->count());
    }
}
