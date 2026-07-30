<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class SiteSettingValueNormalizer
{
    /** @return array<string, mixed> */
    public static function normalize(string $key, mixed $value): array
    {
        $payload = is_array($value) ? $value : [];

        return match ($key) {
            'contact' => self::normalizeContact($payload),
            'socials' => self::normalizeSocials($payload),
            default => $payload,
        };
    }

    /** @param array<string, mixed> $value
     * @return array<string, mixed>
     */
    public static function normalizeContact(array $value): array
    {
        $phones = self::normalizePhoneCollection($value['phones'] ?? []);

        if (filled($value['phone'] ?? null)) {
            $phones->prepend(trim((string) $value['phone']));
        }

        $normalizedPhones = $phones
            ->filter()
            ->unique()
            ->values()
            ->all();

        return array_merge($value, [
            'phone' => $normalizedPhones[0] ?? '',
            'phones' => $normalizedPhones,
            'email' => self::trimmedString($value['email'] ?? ''),
            'address' => self::trimmedString($value['address'] ?? ''),
            'whatsapp' => self::trimmedString($value['whatsapp'] ?? ''),
            'whatsapp_message' => self::stringValue($value['whatsapp_message'] ?? ''),
            'hours' => self::stringValue($value['hours'] ?? ''),
            'lead_email' => self::trimmedString(
                $value['lead_email'] ?? 'safetechgeorgia@gmail.com',
                'safetechgeorgia@gmail.com',
            ),
        ]);
    }

    /** @param array<string, mixed> $value
     * @return array<string, mixed>
     */
    public static function normalizeSocials(array $value): array
    {
        $links = collect(is_array($value['links'] ?? null) ? $value['links'] : [])
            ->map(fn (mixed $item): ?array => self::normalizeSocialLinkItem($item))
            ->filter()
            ->values();

        foreach ($value as $network => $href) {
            $normalizedNetwork = self::normalizeSocialNetwork(is_string($network) ? $network : null);

            if ($normalizedNetwork === null || ! is_string($href) || blank($href)) {
                continue;
            }

            $links->push([
                'network' => $normalizedNetwork,
                'label' => self::socialLabel($normalizedNetwork),
                'href' => trim($href),
            ]);
        }

        $shareButtons = collect(is_array($value['share_buttons'] ?? null) ? $value['share_buttons'] : [])
            ->map(function (mixed $button): ?string {
                if (is_array($button)) {
                    $button = $button['type'] ?? $button['name'] ?? null;
                }

                return is_string($button) && trim($button) !== ''
                    ? strtolower(trim($button))
                    : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return array_merge($value, [
            'links' => $links
                ->unique(fn (array $item): string => "{$item['network']}|{$item['href']}")
                ->values()
                ->all(),
            'share_title' => self::stringValue($value['share_title'] ?? ''),
            'share_buttons' => $shareButtons,
        ]);
    }

    private static function normalizePhoneCollection(mixed $value): Collection
    {
        return collect(is_array($value) ? $value : [])
            ->map(function (mixed $phone): ?string {
                if (is_array($phone)) {
                    $phone = $phone['value'] ?? null;
                }

                if (! is_string($phone)) {
                    return null;
                }

                $phone = trim($phone);

                return $phone !== '' ? $phone : null;
            })
            ->filter();
    }

    /** @return array{network: string, label: string, href: string}|null */
    private static function normalizeSocialLinkItem(mixed $item): ?array
    {
        if (! is_array($item)) {
            return null;
        }

        $network = self::normalizeSocialNetwork(
            is_string($item['network'] ?? null) ? $item['network'] : null,
        );
        $href = is_string($item['href'] ?? null) ? trim((string) $item['href']) : '';

        if ($network === null || $href === '') {
            return null;
        }

        $label = is_string($item['label'] ?? null) && trim((string) $item['label']) !== ''
            ? trim((string) $item['label'])
            : self::socialLabel($network);

        return [
            'network' => $network,
            'label' => $label,
            'href' => $href,
        ];
    }

    private static function normalizeSocialNetwork(?string $network): ?string
    {
        $normalized = strtolower(trim((string) $network));

        return match ($normalized) {
            'facebook', 'linkedin', 'instagram', 'tiktok', 'whatsapp', 'email', 'x', 'youtube', 'telegram' => $normalized,
            'twitter' => 'x',
            default => null,
        };
    }

    private static function socialLabel(string $network): string
    {
        return match ($network) {
            'linkedin' => 'LinkedIn',
            'tiktok' => 'TikTok',
            'whatsapp' => 'WhatsApp',
            'youtube' => 'YouTube',
            'x' => 'X',
            default => ucfirst($network),
        };
    }

    private static function stringValue(mixed $value, string $fallback = ''): string
    {
        return is_string($value) ? $value : $fallback;
    }

    private static function trimmedString(mixed $value, string $fallback = ''): string
    {
        return is_string($value) ? trim($value) : $fallback;
    }
}
