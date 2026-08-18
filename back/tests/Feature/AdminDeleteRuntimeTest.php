<?php

namespace Tests\Feature;

use App\Models\CategoryForService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteRuntimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_service_category_can_be_deleted_without_deleting_its_services(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Runtime category',
            'slug' => 'runtime-category',
        ]);
        $service = Service::factory()->create([
            'category_for_service_id' => $category->id,
        ]);

        $this->assertTrue($category->delete());
        $this->assertDatabaseMissing('category_for_services', ['id' => $category->id]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'category_for_service_id' => null,
        ]);
    }

    public function test_a_project_category_can_be_deleted_without_deleting_its_projects(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Runtime projects',
            'slug' => 'runtime-projects',
        ]);
        $project = Project::query()->create([
            'name' => 'Runtime project',
            'title' => 'Runtime project',
            'slug' => 'runtime-project',
            'description' => 'Runtime project description.',
            'seo_description' => 'Runtime project SEO description.',
            'category_id' => $category->id,
            'is_published' => false,
        ]);

        $this->assertTrue($category->delete());
        $this->assertDatabaseMissing('project_categories', ['id' => $category->id]);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'category_id' => null,
        ]);
    }
}
