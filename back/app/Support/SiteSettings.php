<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

final class SiteSettings
{
    /** @return array<string, mixed> */
    public static function all(): array
    {
        return Cache::remember(
            PublicContentCache::key('site-settings'),
            now()->addHour(),
            fn (): array => SiteSetting::query()
                ->public()
                ->get()
                ->mapWithKeys(fn (SiteSetting $setting): array => [$setting->key => $setting->value])
                ->all(),
        );
    }

    /** @return array<string, mixed> */
    public static function value(string $key): array
    {
        $value = self::all()[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    public static function brandingMediaUrl(string $collection): ?string
    {
        if (! in_array($collection, ['logo', 'footer_logo', 'favicon', 'default_image'], true)) {
            return null;
        }

        return Cache::remember(
            PublicContentCache::key("branding-media:{$collection}"),
            now()->addHour(),
            fn (): ?string => SiteSetting::query()
                ->where('key', 'branding')
                ->first()?->brandingMediaUrl($collection),
        );
    }

    public static function businessProfile(): object
    {
        $contact = self::value('contact');
        $seo = self::value('seo');
        $socials = self::value('socials');
        $links = is_array($socials['links'] ?? null) ? $socials['links'] : [];
        $socialUrls = [];

        foreach ($links as $link) {
            if (! is_array($link) || blank($link['network'] ?? null) || blank($link['href'] ?? null)) {
                continue;
            }

            $socialUrls[(string) $link['network']] = (string) $link['href'];
        }

        return (object) [
            'phone' => $contact['phone'] ?? null,
            'email' => $contact['email'] ?? null,
            'address' => $contact['address'] ?? null,
            'city' => $seo['city'] ?? null,
            'country' => $seo['country'] ?? 'GE',
            'postal_code' => $seo['postal_code'] ?? null,
            'lat' => $seo['lat'] ?? null,
            'lng' => $seo['lng'] ?? null,
            'open_time' => $seo['open_time'] ?? null,
            'close_time' => $seo['close_time'] ?? null,
            'facebook' => $socialUrls['facebook'] ?? null,
            'instagram' => $socialUrls['instagram'] ?? null,
            'linkedin' => $socialUrls['linkedin'] ?? null,
            'share_title' => $socials['share_title'] ?? null,
            'share_buttons' => is_array($socials['share_buttons'] ?? null)
                ? $socials['share_buttons']
                : [],
        ];
    }
}
