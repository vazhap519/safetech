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
            $current = $this->normalizeExistingValue($key, $current);
            $merged = $this->mergeMissingValues($current, $defaults);

            $setting->forceFill([
                'group' => filled($setting->group) ? $setting->group : 'general',
                'value' => $merged,
                'is_public' => true,
            ])->save();
        }
    }

    private function normalizeExistingValue(string $key, array $value): array
    {
        if ($key !== 'socials') {
            return $value;
        }

        if (is_array($value['links'] ?? null)) {
            $value['links'] = collect($value['links'])
                ->map(function (mixed $link): mixed {
                    if (! is_array($link)) {
                        return $link;
                    }

                    return array_merge([
                        'enabled' => true,
                        'open_in_new_tab' => true,
                    ], $link);
                })
                ->values()
                ->all();
        }

        if (is_array($value['share_buttons'] ?? null)) {
            $value['share_buttons'] = collect($value['share_buttons'])
                ->map(function (mixed $button): mixed {
                    if (is_string($button)) {
                        $button = ['type' => $button];
                    }

                    if (! is_array($button)) {
                        return $button;
                    }

                    $type = strtolower(trim((string) ($button['type'] ?? $button['name'] ?? '')));
                    $type = match ($type) {
                        'twitter' => 'x',
                        'link' => 'copy',
                        'share' => 'native',
                        default => $type,
                    };

                    return array_merge([
                        'type' => $type,
                        'label' => '',
                        'enabled' => true,
                    ], $button, ['type' => $type]);
                })
                ->values()
                ->all();
        }

        return $value;
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
                'share_enabled' => true,
                'share_on_services' => true,
                'share_on_projects' => true,
                'share_title_ka' => 'გაზიარება',
                'share_title_en' => 'Share',
                'share_title_ru' => 'Поделиться',
                'share_buttons' => [
                    ['type' => 'facebook', 'label' => '', 'enabled' => true],
                    ['type' => 'whatsapp', 'label' => '', 'enabled' => true],
                    ['type' => 'telegram', 'label' => '', 'enabled' => true],
                    ['type' => 'linkedin', 'label' => '', 'enabled' => true],
                    ['type' => 'x', 'label' => '', 'enabled' => true],
                    ['type' => 'copy', 'label' => '', 'enabled' => true],
                ],
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
