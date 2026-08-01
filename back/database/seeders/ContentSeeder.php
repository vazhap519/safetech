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
        foreach ($this->defaultSiteSettings() as $key => $defaults) {
            $setting = SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                [
                    'group' => 'general',
                    'value' => $defaults,
                    'is_public' => true,
                ],
            );

            if ($setting->wasRecentlyCreated) {
                continue;
            }

            $current = is_array($setting->value) ? $setting->value : [];
            $merged = $this->mergeMissingValues($current, $defaults);

            $setting->forceFill([
                'group' => filled($setting->group) ? $setting->group : 'general',
                'value' => $merged,
                'is_public' => true,
            ])->save();
        }
    }

    private function mergeMissingValues(array $current, array $defaults): array
    {
        if (array_is_list($defaults)) {
            return $current === [] ? $defaults : $current;
        }

        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $current)) {
                $current[$key] = $default;

                continue;
            }

            if (is_array($default) && is_array($current[$key])) {
                $current[$key] = $this->mergeMissingValues($current[$key], $default);
            }
        }

        return $current;
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
