<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_published_services_and_projects_are_not_publicly_exposed(): void
    {
        $emptyServiceCategory = CategoryForService::query()->create([
            'name' => 'Empty services',
            'slug' => 'empty-services',
        ]);
        $serviceCategory = CategoryForService::query()->create([
            'name' => 'Security',
            'slug' => 'security',
        ]);

        Service::query()->create([
            'category_for_service_id' => $emptyServiceCategory->id,
            'slug' => 'empty-service',
            'name' => 'Empty service',
            'title' => 'Empty service',
            'description' => '   ',
            'short_description' => null,
            'long_description' => null,
            'is_published' => true,
        ]);
        Service::query()->create([
            'category_for_service_id' => $serviceCategory->id,
            'slug' => 'camera-installation',
            'name' => 'Camera installation',
            'title' => 'Camera installation',
            'description' => 'A complete camera installation service.',
            'short_description' => 'A complete camera installation service.',
            'is_published' => true,
        ]);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'camera-installation');
        $this->getJson('/api/services/empty-service')->assertNotFound();
        $this->getJson('/api/service-categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'security');

        $emptyProjectCategory = ProjectCategory::query()->create([
            'name' => 'Empty projects',
            'slug' => 'empty-projects',
        ]);
        $projectCategory = ProjectCategory::query()->create([
            'name' => 'Offices',
            'slug' => 'offices',
        ]);

        Project::query()->create([
            'category_id' => $emptyProjectCategory->id,
            'slug' => 'empty-project',
            'name' => 'Empty project',
            'title' => 'Empty project',
            'description' => '   ',
            'excerpt' => null,
            'content' => null,
            'is_published' => true,
        ]);
        Project::query()->create([
            'category_id' => $projectCategory->id,
            'slug' => 'office-network',
            'name' => 'Office network',
            'title' => 'Office network',
            'description' => 'A complete office network deployment.',
            'is_published' => true,
        ]);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'office-network');
        $this->getJson('/api/projects/empty-project')->assertNotFound();
        $this->getJson('/api/project-categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'offices');
    }
}
