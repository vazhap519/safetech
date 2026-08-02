<?php

namespace Tests\Feature;

use App\Filament\Support\CmsMediaUpload;
use App\Models\Partner;
use App\Models\Project;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\CmsMedia;
use App\Support\DeploymentInfo;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsMediaUploadConfigurationTest extends TestCase
{
    public function test_livewire_temporary_uploads_accept_cms_images(): void
    {
        $this->assertSame('local', config('livewire.temporary_file_upload.disk'));
        $this->assertSame('livewire-tmp', config('livewire.temporary_file_upload.directory'));
        $this->assertSame(10, config('livewire.temporary_file_upload.max_upload_time'));

        $rules = config('livewire.temporary_file_upload.rules', []);

        $this->assertContains('max:'.CmsMedia::MAX_FILE_SIZE_KB, $rules);
        $this->assertContains(
            'mimetypes:'.implode(',', CmsMedia::IMAGE_MIME_TYPES),
            $rules,
        );
    }

    public function test_all_active_cms_media_collections_use_the_public_disk(): void
    {
        $models = [
            new Partner,
            new Project,
            new SeoPage,
            new Service,
            new SiteSetting,
            new TeamMember,
            new Testimonial,
        ];

        foreach ($models as $model) {
            foreach ($model->getRegisteredMediaCollections() as $collection) {
                $this->assertSame(
                    'public',
                    $collection->diskName,
                    sprintf('%s:%s must use the public disk.', $model::class, $collection->name),
                );
            }
        }
    }

    public function test_proxy_and_php_limits_are_larger_than_the_cms_limit(): void
    {
        $phpConfiguration = file_get_contents(public_path('.user.ini'));
        $nginxConfiguration = file_get_contents(CmsMediaUpload::nginxConfigSourcePath());

        $this->assertIsString($phpConfiguration);
        $this->assertStringContainsString('file_uploads = On', $phpConfiguration);
        $this->assertStringContainsString('upload_max_filesize = 20M', $phpConfiguration);
        $this->assertStringContainsString('post_max_size = 25M', $phpConfiguration);
        $this->assertStringContainsString('max_file_uploads = 50', $phpConfiguration);

        $this->assertIsString($nginxConfiguration);
        $this->assertStringContainsString('client_max_body_size 25m;', $nginxConfiguration);
        $this->assertStringContainsString('client_body_timeout 120s;', $nginxConfiguration);
    }

    public function test_upload_smoke_command_validates_a_two_megabyte_png_and_storage_round_trip(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $this->artisan('cms:upload-smoke')
            ->expectsOutput('CMS upload smoke test passed.')
            ->assertSuccessful();

        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_upload_smoke_command_checks_the_public_deployment_and_livewire_endpoint(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $commit = str_repeat('a', 40);
        DeploymentInfo::writeCommit($commit);

        Http::fake(function (Request $request) use ($commit) {
            if (str_contains($request->url(), '/api/health')) {
                return Http::response(['status' => 'ok', 'commit' => $commit]);
            }

            if (str_contains($request->url(), '/livewire/upload-file')) {
                return Http::response(['paths' => ['livewire-tmp/smoke.png']]);
            }

            return Http::response(status: 404);
        });

        $this->artisan('cms:upload-smoke', [
            '--http-base-url' => 'https://api.example.test',
        ])
            ->expectsOutput('CMS upload smoke test passed.')
            ->assertSuccessful();

        Http::assertSentCount(2);
        Http::assertSent(
            fn (Request $request): bool => str_contains($request->url(), '/livewire/upload-file')
                && $request->hasFile('files[]', 'safetech-upload-smoke.png'),
        );
    }

    public function test_upload_smoke_command_rejects_a_stale_public_backend(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        DeploymentInfo::writeCommit(str_repeat('a', 40));

        Http::fake([
            'https://api.example.test/api/health*' => Http::response([
                'status' => 'ok',
                'commit' => str_repeat('b', 40),
            ]),
        ]);

        $this->artisan('cms:upload-smoke', [
            '--http-base-url' => 'https://api.example.test',
        ])
            ->expectsOutputToContain('The public API is serving commit')
            ->assertFailed();
    }
}
