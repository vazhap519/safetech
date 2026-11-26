<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use App\Support\Seo\CanonicalLocalSeo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalLocalSeoScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_keeps_only_the_seven_services_in_the_six_confirmed_locations(): void
    {
        $canonical = $this->service('security-camera-installation');
        $legacy = $this->service('network-cable-installation');

        $kept = $this->landing($canonical, 'tbilisi');
        $legacyLanding = $this->landing($legacy, 'tbilisi');
        $neighbourhoodLanding = $this->landing($canonical, 'vake');

        $this->assertSame(2, CanonicalLocalSeo::prune());
        $this->assertDatabaseHas('local_service_landings', ['id' => $kept->id]);
        $this->assertDatabaseMissing('local_service_landings', ['id' => $legacyLanding->id]);
        $this->assertDatabaseMissing('local_service_landings', ['id' => $neighbourhoodLanding->id]);
        $this->assertSame([$kept->id], LocalServiceLanding::query()->canonical()->pluck('id')->all());
    }

    public function test_public_api_hides_noncanonical_service_and_location_pages(): void
    {
        $canonical = $this->service('security-camera-installation');
        $legacy = $this->service('network-cable-installation');

        $this->landing($canonical, 'tbilisi');
        $this->landing($legacy, 'tbilisi');
        $this->landing($canonical, 'vake');

        $this->getJson('/api/local-service-landings?view=summary')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.service.slug', 'security-camera-installation')
            ->assertJsonPath('data.0.locationSlug', 'tbilisi');

        $this->getJson('/api/local-service-landings/network-cable-installation/tbilisi')->assertNotFound();
        $this->getJson('/api/local-service-landings/security-camera-installation/vake')->assertNotFound();
    }

    private function service(string $slug): Service
    {
        return Service::query()->create([
            'slug' => $slug,
            'name' => $slug,
            'title' => $slug,
            'description' => 'Published service description.',
            'is_published' => true,
        ]);
    }

    private function landing(Service $service, string $location): LocalServiceLanding
    {
        return LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => $location,
            'location_name' => ucfirst($location),
            'title' => "{$service->name} {$location}",
            'content' => 'Unique local service content for the confirmed service area.',
            'is_published' => true,
            'noindex' => false,
        ]);
    }
}
