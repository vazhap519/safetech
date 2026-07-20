<?php

namespace App\Support\Seo;

use Illuminate\Support\Arr;

final class SeoAudit
{
    public const LOCALES = ['ka', 'en', 'ru'];

    /** @return array{score: int, issues: array<int, string>, notes: array<int, string>} */
    public static function analyze(array $state): array
    {
        $issues = [];
        $earned = 0.0;

        $key = trim((string) ($state['key'] ?? ''));
        $slug = trim((string) ($state['slug'] ?? ''));
        $title = self::clean($state['title'] ?? null);
        $description = self::clean($state['description'] ?? null);

        $earned += self::portion(
            10,
            [$key !== '', $slug !== ''],
            $issues,
            'გვერდისა და URL-ის იდენტიფიკატორები სრულად შეავსეთ.',
        );
        $earned += self::portion(
            10,
            [self::isCanonicalPath($slug)],
            $issues,
            'URL უნდა იწყებოდეს /-ით და არ შეიცავდეს query/hash ნაწილს.',
        );
        $earned += self::portion(
            20,
            [$title !== '', $description !== ''],
            $issues,
            'ქართული fallback სათაური და აღწერა სავალდებულოა.',
        );

        $localizedTitles = [];
        $localizedDescriptions = [];

        foreach (self::LOCALES as $locale) {
            $localizedTitles[$locale] = self::localized($state, 'title', $locale);
            $localizedDescriptions[$locale] = self::localized($state, 'description', $locale);
        }

        $earned += self::portion(
            20,
            [
                ...array_map(fn (string $value): bool => $value !== '', $localizedTitles),
                ...array_map(fn (string $value): bool => $value !== '', $localizedDescriptions),
            ],
            $issues,
            'ქართული, ინგლისური და რუსული title/description დამოუკიდებლად შეავსეთ.',
        );
        $earned += self::portion(
            10,
            array_map(fn (string $value): bool => self::readableTitleLength($value), $localizedTitles),
            $issues,
            'ერთ ან მეტ ენაზე სათაური ზედმეტად მოკლე ან გრძელია; პრაქტიკული დიაპაზონია 15-70 სიმბოლო.',
        );
        $earned += self::portion(
            10,
            array_map(fn (string $value): bool => self::readableDescriptionLength($value), $localizedDescriptions),
            $issues,
            'ერთ ან მეტ ენაზე აღწერა ზედმეტად მოკლე ან გრძელია; პრაქტიკული დიაპაზონია 50-180 სიმბოლო.',
        );
        $earned += self::portion(
            10,
            [self::hasValidSchema($state)],
            $issues,
            'აირჩიეთ შესაბამისი Schema ტიპი ან გაასწორეთ JSON override.',
        );

        $socialChecks = [];
        foreach (self::LOCALES as $locale) {
            $socialChecks[] = self::localized($state, 'og_title', $locale, $localizedTitles[$locale]) !== '';
            $socialChecks[] = self::localized($state, 'og_description', $locale, $localizedDescriptions[$locale]) !== '';
        }
        $earned += self::portion(
            10,
            $socialChecks,
            $issues,
            'Open Graph preview-ს ერთ ან მეტ ენაზე სათაური ან აღწერა აკლია.',
        );

        $notes = [
            'ეს არის SafeTech-ის შიდა SEO QA ქულა და არა Google-ის ოფიციალური შეფასება.',
            'Meta keywords Google-ის რეიტინგის სიგნალი არ არის; ველი მხოლოდ კონტენტის დაგეგმვისთვის რჩება.',
        ];

        if ((bool) ($state['noindex'] ?? false)) {
            $notes[] = 'Noindex ჩართულია: გვერდი საძიებო ინდექსში შეგნებულად არ მოხვდება.';
        }

        return [
            'score' => max(0, min(100, (int) round($earned))),
            'issues' => array_values(array_unique($issues)),
            'notes' => $notes,
        ];
    }

    /** @return array<string, mixed> */
    public static function normalize(array $state): array
    {
        $key = trim((string) ($state['key'] ?? ''));
        $state['slug'] = self::slugForKey($key);
        $state['schema_type'] = filled($state['schema_type'] ?? null)
            ? $state['schema_type']
            : self::recommendedSchemaType($key);

        foreach (['title', 'description'] as $field) {
            $state[$field] = self::clean($state[$field] ?? null);
        }

        if ($state['title'] === '') {
            $state['title'] = self::clean(Arr::get($state, 'translations.fields.title.ka'));
        }

        if ($state['description'] === '') {
            $state['description'] = self::clean(Arr::get($state, 'translations.fields.description.ka'));
        }

        foreach (self::LOCALES as $locale) {
            foreach (['title', 'description', 'og_title', 'og_description'] as $field) {
                $path = "translations.fields.{$field}.{$locale}";
                Arr::set($state, $path, self::clean(Arr::get($state, $path)));
            }

            if ($locale === 'ka') {
                Arr::set(
                    $state,
                    'translations.fields.title.ka',
                    self::localized($state, 'title', 'ka'),
                );
                Arr::set(
                    $state,
                    'translations.fields.description.ka',
                    self::localized($state, 'description', 'ka'),
                );
            }

            $localizedTitle = self::localized($state, 'title', $locale);
            $localizedDescription = self::localized($state, 'description', $locale);

            if (self::clean(Arr::get($state, "translations.fields.og_title.{$locale}")) === '') {
                Arr::set($state, "translations.fields.og_title.{$locale}", $localizedTitle);
            }

            if (self::clean(Arr::get($state, "translations.fields.og_description.{$locale}")) === '') {
                Arr::set($state, "translations.fields.og_description.{$locale}", $localizedDescription);
            }
        }

        $state['keywords'] = collect($state['keywords'] ?? [])
            ->map(fn ($keyword): string => self::clean(is_array($keyword) ? ($keyword['value'] ?? null) : $keyword))
            ->filter()
            ->unique(fn (string $keyword): string => mb_strtolower($keyword))
            ->map(fn (string $keyword): array => ['value' => $keyword])
            ->values()
            ->all();

        if (is_string($state['schema'] ?? null)) {
            $decoded = json_decode($state['schema'], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $state['schema'] = json_encode(
                    $decoded,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                );
            }
        }

        return $state;
    }

    public static function recommendedSchemaType(string $key): string
    {
        return match ($key) {
            'home' => 'WebSite',
            'about' => 'AboutPage',
            'services', 'projects' => 'CollectionPage',
            'service-calculator' => 'WebApplication',
            'blog' => 'Blog',
            'contact' => 'ContactPage',
            default => 'WebPage',
        };
    }

    private static function slugForKey(string $key): string
    {
        return $key === 'home' ? '/' : '/'.trim($key, '/');
    }

    private static function localized(array $state, string $field, string $locale, string $fallback = ''): string
    {
        $value = self::clean(Arr::get($state, "translations.fields.{$field}.{$locale}"));

        if ($value !== '') {
            return $value;
        }

        if ($locale === 'ka') {
            $rootValue = self::clean($state[$field] ?? null);

            if ($rootValue !== '') {
                return $rootValue;
            }
        }

        return $fallback;
    }

    private static function clean(mixed $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    /** @param array<int, bool> $checks */
    private static function portion(int $weight, array $checks, array &$issues, string $message): float
    {
        $total = count($checks);

        if ($total === 0) {
            return 0;
        }

        $passed = count(array_filter($checks));

        if ($passed !== $total) {
            $issues[] = $message;
        }

        return $weight * ($passed / $total);
    }

    private static function isCanonicalPath(string $slug): bool
    {
        return $slug !== ''
            && str_starts_with($slug, '/')
            && ! str_starts_with($slug, '//')
            && ! str_contains($slug, '?')
            && ! str_contains($slug, '#');
    }

    private static function readableTitleLength(string $value): bool
    {
        $length = mb_strlen($value);

        return $length >= 15 && $length <= 70;
    }

    private static function readableDescriptionLength(string $value): bool
    {
        $length = mb_strlen($value);

        return $length >= 50 && $length <= 180;
    }

    private static function hasValidSchema(array $state): bool
    {
        if (blank($state['schema_type'] ?? null)) {
            return false;
        }

        $schema = $state['schema'] ?? null;

        if (blank($schema)) {
            return true;
        }

        if (is_array($schema)) {
            return $schema !== [];
        }

        json_decode((string) $schema, true);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
