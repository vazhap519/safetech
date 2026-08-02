<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Project;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\CmsMedia;
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

    public function test_php_fpm_limits_are_larger_than_the_cms_limit(): void
    {
        $configuration = file_get_contents(public_path('.user.ini'));

        $this->assertIsString($configuration);
        $this->assertStringContainsString('file_uploads = On', $configuration);
        $this->assertStringContainsString('upload_max_filesize = 20M', $configuration);
        $this->assertStringContainsString('post_max_size = 25M', $configuration);
        $this->assertStringContainsString('max_file_uploads = 50', $configuration);
    }
}
