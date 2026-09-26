<?php

namespace Tests\Feature;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\Service;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CameraLocalSearchMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_strengthens_camera_search_intent_and_moves_verified_project_to_bakuriani(): void
    {
        $this->seed(SystemContentSeeder::class);

        $service = Service::query()->where('slug', 'security-camera-installation')->sole();
        $tbilisi = $this->landing($service, 'tbilisi', 'თბილისი');
        $bakuriani = $this->landing($service, 'bakuriani', 'ბაკურიანი');

        $project = Project::query()->create([
            'slug' => '6-kameriani-tvt-full-color-videosametvalyureo-sistema',
            'name' => '6 კამერიანი TVT Full Color ვიდეოსამეთვალყურეო სისტემა',
            'title' => '6 კამერიანი TVT 4MP Full Color ვიდეოსამეთვალყურეო სისტემა',
            'description' => 'ძველი აღწერა',
            'seo_description' => 'ძველი SEO აღწერა',
            'city' => 'თბილისი',
            'object_type' => 'ავტონაწილების მაღაზია',
            'equipment' => [],
            'is_published' => true,
        ]);

        DB::table('local_service_landing_project')->insert([
            'landing_id' => $tbilisi->getKey(),
            'project_id' => $project->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path(
            'migrations/2026_09_26_180000_strengthen_camera_local_search_and_bakuriani_proof.php',
        );
        $migration->up();
        $migration->up();

        $service->refresh();
        $bakuriani->refresh();
        $project->refresh();

        $this->assertStringContainsString('დაყენება და მონტაჟი', $service->title);
        $this->assertSame(
            'უსაფრთხოების კამერების დაყენება და მონტაჟი ბაკურიანში',
            $bakuriani->title,
        );
        $this->assertSame('კამერების დაყენება ბაკურიანში', $bakuriani->primary_keyword);
        $this->assertContains('კამერების მონტაჟი ბაკურიანში', $bakuriani->keywords);
        $this->assertSame(
            'Security Camera Installation and Setup in Bakuriani',
            data_get($bakuriani->translations, 'fields.title.en'),
        );

        $this->assertSame('ბაკურიანი', $project->city);
        $this->assertSame('კოტეჯი', $project->object_type);
        $this->assertSame('6', data_get($project->equipment, '0.quantity'));
        $this->assertSame('1', data_get($project->equipment, '1.quantity'));
        $this->assertSame('Bakuriani', data_get($project->translations, 'fields.city.en'));
        $this->assertStringContainsString('6× TVT 4MP Full Color', $project->seo_description);

        $this->assertDatabaseMissing('local_service_landing_project', [
            'landing_id' => $tbilisi->getKey(),
            'project_id' => $project->getKey(),
        ]);
        $this->assertDatabaseHas('local_service_landing_project', [
            'landing_id' => $bakuriani->getKey(),
            'project_id' => $project->getKey(),
        ]);
        $this->assertSame(
            1,
            DB::table('local_service_landing_project')->where('project_id', $project->getKey())->count(),
        );
    }

    private function landing(Service $service, string $slug, string $name): LocalServiceLanding
    {
        return LocalServiceLanding::query()->create([
            'service_id' => $service->getKey(),
            'location_slug' => $slug,
            'location_name' => $name,
            'title' => "უსაფრთხოების კამერების მონტაჟი {$name}",
            'content' => 'ობიექტის შეფასება, კაბელირება, მონტაჟი და გამართვა.',
            'primary_keyword' => "კამერების მონტაჟი {$name}",
            'keywords' => ["კამერების მონტაჟი {$name}"],
            'is_published' => true,
            'noindex' => false,
            'published_at' => now(),
        ]);
    }
}
