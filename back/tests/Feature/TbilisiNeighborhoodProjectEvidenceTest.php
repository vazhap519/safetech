<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\Service;
use Database\Seeders\TbilisiNeighborhoodSeoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TbilisiNeighborhoodProjectEvidenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_project_is_never_auto_linked_based_on_a_city_name_alone(): void
    {
        $service = Service::query()->create([
            'slug' => 'security-camera-installation',
            'name' => 'კამერები',
            'title' => 'კამერები',
            'description' => 'კამერების მონტაჟი',
            'is_published' => true,
        ]);
        $project = Project::query()->create([
            'slug' => 'varketili-project-review-required',
            'name' => 'ვარკეთილის პროექტი',
            'title' => 'ვარკეთილის პროექტი',
            'description' => 'პროექტის აღწერა',
            'city' => 'თბილისი',
            'is_published' => true,
        ]);

        $this->seed(TbilisiNeighborhoodSeoSeeder::class);

        $landing = LocalServiceLanding::query()
            ->where('service_id', $service->getKey())
            ->where('location_slug', 'varketili')
            ->firstOrFail();

        $this->assertFalse($landing->is_published);
        $this->assertTrue($landing->noindex);
        $this->assertFalse($landing->projects()->whereKey($project->getKey())->exists());

        // A real editor explicitly confirms the match before associating it.
        $landing->projects()->attach($project->getKey());

        $this->assertTrue($landing->projects()->whereKey($project->getKey())->exists());
        $this->seed(TbilisiNeighborhoodSeoSeeder::class);
        $this->assertSame(1, $landing->projects()->count());
    }
}
