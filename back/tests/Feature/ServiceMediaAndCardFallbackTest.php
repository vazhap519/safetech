<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceMediaAndCardFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_card_translations_are_filled_from_service_content(): void
    {
        $service = Service::query()->create([
            'slug' => 'windows-installation-test',
            'name' => 'ოპერაციული სისტემების ინსტალაცია',
            'title' => 'Windows-ის ინსტალაცია',
            'description' => 'Windows-ის ინსტალაცია, დრაივერები და პროგრამები.',
            'short_description' => 'Windows-ის ინსტალაცია, დრაივერები და პროგრამები.',
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'ოპერაციული სისტემების ინსტალაცია',
                        'en' => 'Operating System Installation',
                        'ru' => 'Установка операционных систем',
                    ],
                    'description' => [
                        'ka' => 'Windows-ის ინსტალაცია, დრაივერები და პროგრამები.',
                        'en' => 'Windows installation, drivers, and software.',
                        'ru' => 'Установка Windows, драйверов и программ.',
                    ],
                ],
            ],
        ]);

        $service->refresh();

        $this->assertSame(
            'ოპერაციული სისტემების ინსტალაცია',
            data_get($service->translations, 'fields.card.title.ka'),
        );
        $this->assertSame(
            'Operating System Installation',
            data_get($service->translations, 'fields.card.title.en'),
        );
        $this->assertSame(
            'Установка Windows, драйверов и программ.',
            data_get($service->translations, 'fields.card.description.ru'),
        );
    }

    public function test_custom_card_content_is_not_overwritten(): void
    {
        $service = Service::query()->create([
            'slug' => 'custom-card-test',
            'name' => 'სერვისი',
            'title' => 'სერვისი',
            'description' => 'ძირითადი აღწერა',
            'short_description' => 'ძირითადი აღწერა',
            'translations' => [
                'fields' => [
                    'name' => ['ka' => 'სერვისი'],
                    'description' => ['ka' => 'ძირითადი აღწერა'],
                    'card' => [
                        'title' => ['ka' => 'სპეციალური ბარათის სათაური'],
                        'description' => ['ka' => 'სპეციალური ბარათის აღწერა'],
                    ],
                ],
            ],
        ]);

        $service->refresh();

        $this->assertSame(
            'სპეციალური ბარათის სათაური',
            data_get($service->translations, 'fields.card.title.ka'),
        );
        $this->assertSame(
            'სპეციალური ბარათის აღწერა',
            data_get($service->translations, 'fields.card.description.ka'),
        );
    }

    public function test_public_php_upload_limits_support_cms_images(): void
    {
        $configuration = file_get_contents(public_path('.user.ini'));

        $this->assertIsString($configuration);
        $this->assertStringContainsString('upload_max_filesize = 20M', $configuration);
        $this->assertStringContainsString('post_max_size = 25M', $configuration);
        $this->assertStringContainsString('memory_limit = 512M', $configuration);
    }
}
