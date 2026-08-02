<?php

namespace App\Support;

use Illuminate\Support\Arr;

class SocialLinks
{
    public static function frontendUrl(string $path = ''): string
    {
        $baseUrl = rtrim((string) (config('app.frontend_url') ?: config('app.url')), '/');
        $path = '/'.ltrim($path, '/');

        return $baseUrl.($path === '/' ? '' : $path);
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
                'url' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
                'color' => 'bg-red-600',
                'icon' => 'FaPinterest',
            ],
            'x' => [
                'type' => 'x',
                'name' => 'X',
                'url' => 'https://twitter.com/intent/tweet?url={url}&text={title}',
                'color' => 'bg-black',
                'icon' => 'FaTwitter',
            ],
            'viber' => [
                'type' => 'viber',
                'name' => 'Viber',
                'url' => 'viber://forward?text={title}%20{url}',
                'color' => 'bg-purple-600',
                'icon' => 'FaViber',
            ],
            'email' => [
                'type' => 'email',
                'name' => 'Email',
                'url' => 'mailto:?subject={title}&body={title}%0A{url}',
                'color' => 'bg-gray-600',
                'icon' => 'FaEnvelope',
            ],
            'native' => [
                'type' => 'native',
                'name' => 'Share',
                'url' => '{url}',
                'color' => 'bg-teal-600',
                'icon' => 'FaShareAlt',
            ],
            'copy' => [
                'type' => 'copy',
                'name' => 'Copy link',
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
                $enabled = ! is_array($button) || Arr::get($button, 'enabled', true) !== false;

                if (! $enabled) {
                    return null;
                }

                $type = match (strtolower((string) $type)) {
                    'twitter' => 'x',
                    'link' => 'copy',
                    'share' => 'native',
                    default => strtolower((string) $type),
                };
                $definition = $definitions[$type] ?? null;

                if (! $definition) {
                    return null;
                }

                $customLabel = is_array($button) ? trim((string) Arr::get($button, 'label', '')) : '';

                if ($customLabel !== '') {
                    $definition['name'] = $customLabel;
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

    public static function socials(array $items = [], ?object $settings = null): array
    {
        $socials = collect($items)
            ->map(fn ($item) => self::normalizeSocialItem($item))
            ->filter()
            ->values();

        $fallbackLinks = is_array($settings?->social_links ?? null)
            ? $settings->social_links
            : [];

        foreach ([
            'facebook' => 'FaFacebook',
            'instagram' => 'FaInstagram',
            'linkedin' => 'FaLinkedin',
            'tiktok' => 'FaTiktok',
            'x' => 'FaTwitter',
            'youtube' => 'FaYoutube',
            'telegram' => 'FaTelegram',
            'whatsapp' => 'FaWhatsapp',
            'viber' => 'FaViber',
            'pinterest' => 'FaPinterest',
            'email' => 'FaEnvelope',
        ] as $field => $icon) {
            $url = self::normalizeNetworkUrl(
                $field,
                $fallbackLinks[$field] ?? ($settings ? data_get($settings, $field) : null),
            );

            if (! $url || $socials->contains(fn ($item) => $item['url'] === $url)) {
                continue;
            }

            $socials->push([
                'icon' => $icon,
                'url' => $url,
                'text' => self::networkLabel($field),
                'bg_color' => 'rgba(255,255,255,0.1)',
                'hover_color' => '#00C2A8',
            ]);
        }

        return $socials->values()->all();
    }

    public static function sameAs(?object $settings = null): array
    {
        $configuredLinks = is_array($settings?->social_links ?? null)
            ? $settings->social_links
            : [];

        return collect($configuredLinks)
            ->except(['email', 'whatsapp', 'viber'])
            ->map(fn ($url): ?string => self::normalizeUrl(is_string($url) ? $url : null))
            ->filter()
            ->values()
            ->all();
    }

    public static function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return null;
        }

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (preg_match('#^(https?:|mailto:|tel:|viber:)#i', $url)) {
            return $url;
        }

        return 'https://'.ltrim($url, '/');
    }

    private static function normalizeNetworkUrl(string $network, mixed $value): ?string
    {
        $url = trim((string) $value);

        if ($url === '' || $url === '#') {
            return null;
        }

        if ($network === 'email') {
            return str_starts_with($url, 'mailto:') ? $url : 'mailto:'.$url;
        }

        if ($network === 'whatsapp' && ! preg_match('#^https?://#i', $url)) {
            $digits = preg_replace('/\D+/', '', $url) ?? '';

            return $digits !== '' ? 'https://wa.me/'.$digits : null;
        }

        if ($network === 'viber' && ! preg_match('#^(https?|viber):#i', $url)) {
            $digits = preg_replace('/\D+/', '', $url) ?? '';

            return $digits !== '' ? 'viber://chat?number=%2B'.$digits : null;
        }

        return self::normalizeUrl($url);
    }

    private static function normalizeSocialItem(mixed $item): ?array
    {
        if (! is_array($item)) {
            return null;
        }

        $url = self::normalizeUrl(Arr::get($item, 'url'));

        if (! $url) {
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

    private static function networkLabel(string $network): string
    {
        return match ($network) {
            'linkedin' => 'LinkedIn',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'youtube' => 'YouTube',
            'pinterest' => 'Pinterest',
            'viber' => 'Viber',
            'x' => 'X',
            default => ucfirst($network),
        };
    }
}
