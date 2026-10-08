<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Service;
use Database\Seeders\TbilisiNeighborhoodSeoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TbilisiNeighborhoodSeoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stages_fifteen_private_localized_drafts_without_overwriting_editor_changes(): void
    {
        foreach (['security-camera-installation', 'intercom-access-control-installation', 'barrier-gate-installation'] as $slug) {
            Service::query()->create([
                'slug' => $slug,
                'name' => $slug,
                'title' => $slug,
                'description' => 'Test service',
                'is_published' => true,
            ]);
        }

        $this->seed(TbilisiNeighborhoodSeoSeeder::class);

        $this->assertSame(15, LocalServiceLanding::query()->count());
        $this->assertSame(0, LocalServiceLanding::query()->where('is_published', true)->count());
        $this->assertSame(15, LocalServiceLanding::query()->where('noindex', true)->count());

        $landing = LocalServiceLanding::query()->where('location_slug', 'gldani')->firstOrFail();
        $this->assertSame('Gldani', data_get($landing->translations, 'fields.locationName.en'));
        $this->assertSame('Глдани', data_get($landing->translations, 'fields.locationName.ru'));
        $landing->update(['title' => 'Reviewed editorial title']);

        $this->seed(TbilisiNeighborhoodSeoSeeder::class);

        $this->assertSame(15, LocalServiceLanding::query()->count());
        $this->assertSame('Reviewed editorial title', $landing->fresh()->title);
    }
}
