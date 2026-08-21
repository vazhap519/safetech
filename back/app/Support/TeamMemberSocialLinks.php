<?php

namespace App\Support;

final class TeamMemberSocialLinks
{
    /** @var array<int, string> */
    private const NETWORKS = [
        'facebook',
        'instagram',
        'linkedin',
        'tiktok',
        'x',
        'youtube',
        'telegram',
        'whatsapp',
        'viber',
        'pinterest',
        'email',
    ];

    /**
     * Convert legacy key/value records and the Admin repeater rows into one
     * public-safe network => link map.
     *
     * @return array<string, string>
     */
    public static function normalize(mixed $socials): array
    {
        if (! is_array($socials)) {
            return [];
        }

        $normalized = [];

        foreach ($socials as $network => $value) {
            if (is_array($value)) {
                $network = $value['network'] ?? $network;
                $value = $value['href'] ?? $value['url'] ?? null;
            }

            $network = self::network($network);
            $href = is_string($value) ? trim($value) : '';

            if ($network === null || $href === '' || self::isUnsafeHref($href)) {
                continue;
            }

            // A network can only have one visible profile. Keep the first row
            // so the form order controls the result predictably.
            $normalized[$network] ??= $href;
        }

        return $normalized;
    }

    /**
     * Shape existing JSON records for the selectable Admin repeater.
     *
     * @return array<int, array{network: string, href: string}>
     */
    public static function formRows(mixed $socials): array
    {
        return collect(self::normalize($socials))
            ->map(fn (string $href, string $network): array => [
                'network' => $network,
                'href' => $href,
            ])
            ->values()
            ->all();
    }

    private static function network(mixed $network): ?string
    {
        if (! is_string($network)) {
            return null;
        }

        $network = strtolower(trim($network));

        if ($network === 'twitter') {
            $network = 'x';
        }

        return in_array($network, self::NETWORKS, true) ? $network : null;
    }

    private static function isUnsafeHref(string $href): bool
    {
        return preg_match('/^(?:javascript|data|vbscript):/i', $href) === 1;
    }
}
