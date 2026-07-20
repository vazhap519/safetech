<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyDemoContentMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_unpublishes_unchanged_demo_records(): void
    {
        Service::query()->create([
            'slug' => 'cctv',
            'name' => 'CCTV',
            'title' => 'CCTV installation and monitoring',
            'description' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.',
            'seo_description' => 'Demo SEO',
            'is_published' => true,
        ]);
        Service::query()->create([
            'slug' => 'networking',
            'name' => 'Custom networking',
            'title' => 'Network Infrastructure',
            'description' => 'Administrator-managed networking description.',
            'seo_description' => 'Custom SEO',
            'is_published' => true,
        ]);
        $translatedService = Service::query()->create([
            'slug' => 'it-support',
            'name' => 'IT Support',
            'title' => 'Managed IT support for business',
            'description' => 'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
            'seo_description' => 'Demo SEO',
            'is_published' => true,
        ]);
        DB::table('services')
            ->where('id', $translatedService->getKey())
            ->update([
                'translations' => json_encode([
                    'fields' => [
                        'title' => ['ka' => 'ადმინისტრატორის თარგმანი'],
                    ],
                ], JSON_THROW_ON_ERROR),
                'updated_at' => now()->addMinute(),
            ]);
        Project::query()->create([
            'slug' => 'office-network-upgrade',
            'name' => 'Office Network Upgrade',
            'title' => 'Office Network Upgrade',
            'description' => 'A complete network refresh with structured cabling, managed switching, and Wi-Fi coverage.',
            'seo_description' => 'Demo SEO',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $migration = require database_path(
            'migrations/2026_07_20_000006_unpublish_legacy_demo_content.php',
        );
        $migration->up();

        $this->assertFalse(Service::query()->where('slug', 'cctv')->firstOrFail()->is_published);
        $this->assertTrue(Service::query()->where('slug', 'networking')->firstOrFail()->is_published);
        $this->assertTrue(Service::query()->where('slug', 'it-support')->firstOrFail()->is_published);
        $this->assertFalse(Project::query()->firstOrFail()->is_published);
        $this->assertFalse(Project::query()->firstOrFail()->is_featured);
    }
}
