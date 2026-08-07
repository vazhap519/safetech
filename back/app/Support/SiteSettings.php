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

        if (! is_array($value)) {
            return [];
        }

        return SiteSettingValueNormalizer::normalize($key, $value);
    }

    public static function brandingMediaUrl(string $collection): ?string
    {
        if (! in_array($collection, [
            'logo',
            'footer_logo',
            'favicon',
            'default_image',
            'home_hero',
            'home_infrastructure',
            'services_hero',
            'projects_hero',
            'about_story',
            'contact_intro',
            'contact_support',
        ], true)) {
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

        $phones = collect(is_array($contact['phones'] ?? null) ? $contact['phones'] : [])
            ->map(fn ($phone) => is_array($phone) ? ($phone['value'] ?? null) : $phone)
            ->filter(fn ($phone) => is_string($phone) && trim($phone) !== '')
            ->map(fn (string $phone): string => trim($phone))
            ->values();

        if (filled($contact['phone'] ?? null)) {
            $phones->prepend(trim((string) $contact['phone']));
        }

        $phones = $phones
            ->filter()
            ->unique()
            ->values()
            ->all();

        $primaryPhone = $phones[0] ?? null;

        return (object) [
            'phone' => $primaryPhone,
            'phones' => $phones,
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
            'social_links' => $socialUrls,
            'share_enabled' => (bool) ($socials['share_enabled'] ?? true),
            'share_on_services' => (bool) ($socials['share_on_services'] ?? true),
            'share_on_projects' => (bool) ($socials['share_on_projects'] ?? true),
            'share_title' => $socials['share_title'] ?? null,
            'share_title_ka' => $socials['share_title_ka'] ?? null,
            'share_title_en' => $socials['share_title_en'] ?? null,
            'share_title_ru' => $socials['share_title_ru'] ?? null,
            'share_buttons' => is_array($socials['share_buttons'] ?? null)
                ? $socials['share_buttons']
                : [],
        ];
    }
}
