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

    public function test_list_endpoints_offer_lightweight_backward_compatible_views(): void
    {
        Service::query()->create([
            'slug' => 'camera-installation',
            'name' => 'Camera installation',
            'title' => 'Camera installation',
            'description' => 'A complete camera installation service.',
            'benefits' => [
                ['title' => 'Remote access', 'description' => 'Secure mobile viewing.'],
            ],
            'is_published' => true,
        ]);

        Project::query()->create([
            'slug' => 'office-network',
            'name' => 'Office network',
            'title' => 'Office network',
            'description' => 'A complete office network deployment.',
            'challenges' => [
                ['title' => 'Coverage', 'description' => 'Multiple floors.'],
            ],
            'specs' => [
                ['label' => 'Cabling', 'value' => 'CAT6'],
            ],
            'is_published' => true,
        ]);

        $serviceCard = $this->getJson('/api/services?view=card&locale=en')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'camera-installation');
        $projectSummary = $this->getJson('/api/projects?view=summary&locale=en')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'office-network')
            ->assertJsonPath('data.0.specs.0.value', 'CAT6');

        $this->assertArrayNotHasKey('benefits', $serviceCard->json('data.0'));
        $this->assertArrayNotHasKey('overview', $serviceCard->json('data.0'));
        $this->assertArrayNotHasKey('challenges', $projectSummary->json('data.0'));
        $this->assertArrayNotHasKey('gallery', $projectSummary->json('data.0'));

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonPath('data.0.benefits.0.title', 'Remote access');
        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonPath('data.0.challenges.0.title', 'Coverage');
    }
}
