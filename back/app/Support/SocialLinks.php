<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Arr;

class SocialLinks
{
    public static function frontendUrl(string $path = ''): string
    {
        $baseUrl = rtrim((string) (config('app.frontend_url') ?: config('app.url')), '/');
        $path = '/' . ltrim($path, '/');

        return $baseUrl . ($path === '/' ? '' : $path);
    }

    public static function shareDefinitions(): array
    {
        return [
            'facebook' => [
                'type' => 'facebook',
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                'color' => 'bg-blue-600',
                'icon' => 'FaFacebook',
            ],
            'whatsapp' => [
                'type' => 'whatsapp',
                'name' => 'WhatsApp',
                'url' => 'https://wa.me/?text={title}%20{url}',
                'color' => 'bg-green-500',
                'icon' => 'FaWhatsapp',
            ],
            'telegram' => [
                'type' => 'telegram',
                'name' => 'Telegram',
                'url' => 'https://t.me/share/url?url={url}&text={title}',
                'color' => 'bg-sky-500',
                'icon' => 'FaTelegram',
            ],
            'linkedin' => [
                'type' => 'linkedin',
                'name' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'color' => 'bg-blue-700',
                'icon' => 'FaLinkedin',
            ],
            'pinterest' => [
                'type' => 'pinterest',
                'name' => 'Pinterest',
                'url' => 'https://pinterest.com/pin/create/button/?url={url}',
                'color' => 'bg-red-600',
                'icon' => 'FaPinterest',
            ],
            'twitter' => [
                'type' => 'twitter',
                'name' => 'X',
                'url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
                'color' => 'bg-black',
                'icon' => 'FaTwitter',
            ],
            'link' => [
                'type' => 'link',
                'name' => 'Copy Link',
                'url' => '{url}',
                'color' => 'bg-gray-600',
                'icon' => 'FaLink',
            ],
        ];
    }

    public static function shareButtons(array $configured = [], ?string $url = null, ?string $title = null): array
    {
        $definitions = self::shareDefinitions();

        return collect($configured)
            ->map(function ($button) use ($definitions, $url, $title) {
                $type = is_array($button)
                    ? Arr::get($button, 'type', Arr::get($button, 'name'))
                    : $button;

                $type = strtolower((string) $type);
                $definition = $definitions[$type] ?? null;

                if (!$definition) {
                    return null;
                }

                if ($url) {
                    $definition['url'] = str_replace(
                        ['{url}', '{title}', '{text}'],
                        [rawurlencode($url), rawurlencode((string) $title), rawurlencode((string) $title)],
                        $definition['url']
                    );
                }

                return $definition;
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function socials(array $items = [], ?Setting $settings = null): array
    {
        $socials = collect($items)
            ->map(fn ($item) => self::normalizeSocialItem($item))
            ->filter()
            ->values();

        foreach ([
            'facebook' => 'FaFacebook',
            'instagram' => 'FaInstagram',
            'linkedin' => 'FaLinkedin',
        ] as $field => $icon) {
            $url = self::normalizeUrl($settings ? data_get($settings, $field) : null);

            if (!$url || $socials->contains(fn ($item) => $item['url'] === $url)) {
                continue;
            }

            $socials->push([
                'icon' => $icon,
                'url' => $url,
                'text' => ucfirst($field),
                'bg_color' => 'rgba(255,255,255,0.1)',
                'hover_color' => '#00C2A8',
            ]);
        }

        return $socials->values()->all();
    }

    public static function sameAs(?Setting $settings = null): array
    {
        return array_values(array_filter([
            self::normalizeUrl($settings?->facebook),
            self::normalizeUrl($settings?->instagram),
            self::normalizeUrl($settings?->linkedin),
        ]));
    }

    public static function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return null;
        }

        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (preg_match('#^(https?:|mailto:|tel:|viber:)#i', $url)) {
            return $url;
        }

        return 'https://' . ltrim($url, '/');
    }

    private static function normalizeSocialItem(mixed $item): ?array
    {
        if (!is_array($item)) {
            return null;
        }

        $url = self::normalizeUrl(Arr::get($item, 'url'));

        if (!$url) {
            return null;
        }

        return [
            'icon' => Arr::get($item, 'icon'),
            'url' => $url,
            'text' => Arr::get($item, 'text'),
            'bg_color' => Arr::get($item, 'bg_color'),
            'hover_color' => Arr::get($item, 'hover_color'),
        ];
    }
}
