<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledContentVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_future_projects_and_pages_are_not_publicly_exposed(): void
    {
        $future = now()->addDay();

        Project::query()->create([
            'slug' => 'future-project',
            'name' => 'Future project',
            'title' => 'Future project',
            'description' => 'This project must stay hidden until its publish date.',
            'is_published' => true,
            'published_at' => $future,
        ]);

        Page::query()->create([
            'slug' => 'future-page',
            'title' => 'Future page',
            'content' => 'This page must stay hidden until its publish date.',
            'is_published' => true,
            'published_at' => $future,
        ]);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonMissing(['slug' => 'future-project']);
        $this->getJson('/api/projects/future-project')->assertNotFound();

        $this->getJson('/api/pages')
            ->assertOk()
            ->assertJsonMissing(['slug' => 'future-page']);
        $this->getJson('/api/pages/future-page')->assertNotFound();
    }

    public function test_local_service_landings_only_return_publicly_visible_projects(): void
    {
        $service = Service::query()->create([
            'slug' => 'camera-installation',
            'name' => 'Camera installation',
            'title' => 'Camera installation',
            'description' => 'Professional camera installation.',
            'is_published' => true,
        ]);

        $visibleProject = Project::query()->create([
            'slug' => 'visible-project',
            'name' => 'Visible project',
            'title' => 'Visible project',
            'description' => 'A completed camera installation.',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $futureProject = Project::query()->create([
            'slug' => 'future-related-project',
            'name' => 'Future related project',
            'title' => 'Future related project',
            'description' => 'A scheduled project that must not leak through a local landing.',
            'is_published' => true,
            'published_at' => now()->addDay(),
        ]);

        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'თბილისი',
            'title' => 'Camera installation in Tbilisi',
            'content' => 'Professional camera installation services in Tbilisi.',
            'is_published' => true,
        ]);
        $landing->projects()->attach([$visibleProject->id, $futureProject->id]);

        $this->getJson('/api/local-service-landings/camera-installation/tbilisi')
            ->assertOk()
            ->assertJsonPath('data.projects.0.slug', 'visible-project')
            ->assertJsonMissing(['slug' => 'future-related-project']);
    }
}
