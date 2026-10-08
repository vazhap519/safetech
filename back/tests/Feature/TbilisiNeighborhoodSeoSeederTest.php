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

    public function test_it_stages_twenty_four_private_localized_drafts_without_overwriting_editor_changes(): void
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

        $this->assertSame(24, LocalServiceLanding::query()->count());
        $this->assertSame(0, LocalServiceLanding::query()->where('is_published', true)->count());
        $this->assertSame(0, LocalServiceLanding::query()->publiclyVisible()->count());
        $this->assertSame(24, LocalServiceLanding::query()->where('noindex', true)->count());

        $this->assertSame(
            ['varketili', 'samgori', 'isani', 'vazisubani'],
            LocalServiceLanding::query()->where('service_id', Service::query()->where('slug', 'security-camera-installation')->value('id'))
                ->orderBy('sort_order')->limit(4)->pluck('location_slug')->all(),
        );

        $landing = LocalServiceLanding::query()->where('location_slug', 'gldani')->firstOrFail();
        $this->assertSame('Gldani', data_get($landing->translations, 'fields.locationName.en'));
        $this->assertSame('Глдани', data_get($landing->translations, 'fields.locationName.ru'));
        $varketili = LocalServiceLanding::query()->where('location_slug', 'varketili')->where('service_id', Service::query()->where('slug', 'security-camera-installation')->value('id'))->firstOrFail();
        $samgori = LocalServiceLanding::query()->where('location_slug', 'samgori')->where('service_id', $varketili->service_id)->firstOrFail();
        $this->assertStringContainsString('PoE', $varketili->content);
        $this->assertStringContainsString('NVR', $varketili->content);
        $this->assertNotSame($varketili->content, $samgori->content);
        $this->assertStringContainsString('storage', data_get($samgori->translations, 'fields.content.en'));
        $this->assertSame(0, $varketili->projects()->count());
        $landing->update(['title' => 'Reviewed editorial title']);

        $this->seed(TbilisiNeighborhoodSeoSeeder::class);

        $this->assertSame(15, LocalServiceLanding::query()->count());
        $this->assertSame('Reviewed editorial title', $landing->fresh()->title);
    }
}
