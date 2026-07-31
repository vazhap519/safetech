<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSystemContent();
    }

    protected function seedSystemContent(): void
    {
        foreach ($this->defaultSiteSettings() as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'general',
                    'value' => $value,
                    'is_public' => true,
                ],
            );
        }
    }

    /** @return array<string, array<string, mixed>> */
    private function defaultSiteSettings(): array
    {
        return [
            'branding' => [
                'site_name' => 'SafeTech',
                'tagline' => '',
                'logo' => null,
                'footer_logo' => null,
                'favicon' => null,
                'default_image' => null,
            ],
            'contact' => [
                'phone' => '',
                'phones' => [],
                'email' => '',
                'address' => '',
                'whatsapp' => '',
                'whatsapp_message' => '',
                'hours' => '',
            ],
            'socials' => [
                'links' => [],
            ],
            'seo' => [
                'site_name' => 'SafeTech',
                'default_keywords' => [],
                'robots_index' => true,
                'robots_follow' => true,
            ],
            'integrations' => [
                'marketing_enabled' => false,
                'google_tag_manager_id' => '',
                'google_analytics_id' => '',
                'meta_pixel_id' => '',
                'google_site_verification' => '',
                'bing_site_verification' => '',
                'yandex_site_verification' => '',
                'indexnow_key' => '',
            ],
            'translations' => [
                'entries' => [],
            ],
        ];
    }
}
