<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\Service;
use Database\Seeders\TbilisiCctvLandingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TbilisiCctvLandingSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_upgrades_only_generated_copy_and_links_only_verified_public_tbilisi_projects(): void
    {
        Storage::fake('local');
        $service = Service::query()->create(['slug' => 'security-camera-installation', 'name' => 'CCTV', 'title' => 'CCTV', 'description' => 'CCTV installation', 'is_published' => true]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id, 'location_slug' => 'tbilisi', 'location_name' => 'თბილისი',
            'title' => 'CCTV თბილისი', 'content' => 'უფლებამოსილი პირების დაშვება',
            'is_published' => true, 'noindex' => false,
            'translations' => ['seo' => ['canonical' => 'https://safetech.ge/services/security-camera-installation/tbilisi']],
        ]);
        $project = Project::query()->create([
            'slug' => 'tbilisshi-tp-link-vigi-full-color-kamerebis-montazhi', 'title' => 'Salon CCTV',
            'content' => 'Four installed cameras.', 'city' => 'თბილისი', 'is_published' => true,
        ]);
        Project::query()->create([
            'slug' => '6-kameris-videometvalqureobis-sistema-meurneobis-obiektze-varketili',
            'title' => 'Not a verified Tbilisi record', 'content' => 'Cameras', 'city' => 'ბათუმი', 'is_published' => true,
        ]);
        Project::query()->create([
            'slug' => 'videometvalqureobis-sistemis-aghdgena-gakhokidzis-kuchaze',
            'title' => 'Unpublished record', 'content' => 'Cameras', 'city' => 'თბილისი', 'is_published' => false,
        ]);

        $this->seed(TbilisiCctvLandingSeeder::class);
        $landing->refresh();
        $this->assertStringNotContainsString('უფლებამოსილი პირების დაშვება', $landing->content);
        $this->assertStringContainsString('NVR/DVR', data_get($landing->translations, 'fields.excerpt.en'));
        $this->assertNotEmpty(data_get($landing->faq, '0.translations.ru.answer'));
        $this->assertFalse($landing->noindex);
        $this->assertSame('https://safetech.ge/services/security-camera-installation/tbilisi', data_get($landing->translations, 'seo.canonical'));
        $this->assertEquals([$project->id], $landing->projects()->pluck('projects.id')->all());
        Storage::disk('local')->assertExists('content-backups/2026-10-17-tbilisi-cctv-'.$landing->id.'.json');

        $landing->update(['content' => 'Manually reviewed content after deployment.']);
        $this->seed(TbilisiCctvLandingSeeder::class);
        $this->assertSame('Manually reviewed content after deployment.', $landing->fresh()->content);
        $this->assertCount(1, $landing->projects()->get());
    }
}
