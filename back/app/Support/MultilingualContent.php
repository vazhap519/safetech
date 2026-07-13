<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

final class MultilingualContent
{
    /** @var array<int, string> */
    public const LOCALES = ['ka', 'en', 'ru'];

    public static function mapFrom(mixed $value): array
    {
        $map = [];

        if (is_array($value) && is_array($value['entries'] ?? null) && array_is_list($value['entries'])) {
            foreach ($value['entries'] as $entry) {
                if (! is_array($entry) || blank($entry['key'] ?? null)) {
                    continue;
                }

                self::mergeValues($map, trim((string) $entry['key']), self::localeValues($entry));
            }

            return $map;
        }

        if (! is_array($value)) {
            return [];
        }

        foreach ($value as $key => $entry) {
            if (! is_array($entry) || blank($key)) {
                continue;
            }

            self::mergeValues($map, (string) $key, self::localeValues($entry));
        }

        return $map;
    }

    public static function entriesFromMap(array $map): array
    {
        ksort($map);

        return array_map(
            fn (string $key, array $values): array => [
                'key' => $key,
                'ka' => trim((string) ($values['ka'] ?? '')),
                'en' => trim((string) ($values['en'] ?? '')),
                'ru' => trim((string) ($values['ru'] ?? '')),
            ],
            array_keys($map),
            $map,
        );
    }

    public static function mergeModelFields(array &$map, string $prefix, Model $model, array $fields): void
    {
        foreach ($fields as $translationKey => $fallback) {
            $field = is_string($fallback) ? $translationKey : (string) $translationKey;
            $fallbackValue = is_string($fallback)
                ? data_get($model, $fallback)
                : (is_callable($fallback) ? $fallback($model) : $fallback);

            self::mergeValues(
                $map,
                "{$prefix}.{$field}",
                self::valuesForField($model, $field, $fallbackValue),
            );
        }

        foreach (Arr::get(self::modelTranslations($model), 'entries', []) as $entry) {
            if (! is_array($entry) || blank($entry['key'] ?? null)) {
                continue;
            }

            self::mergeValues($map, "{$prefix}.".trim((string) $entry['key']), self::localeValues($entry));
        }
    }

    public static function valuesForField(Model $model, string $field, mixed $fallback = null): array
    {
        $translations = self::modelTranslations($model);
        $values = [];

        foreach (self::LOCALES as $locale) {
            $value = Arr::get($translations, "fields.{$field}.{$locale}");
            $legacyValue = Arr::get($translations, "{$locale}.{$field}");

            $values[$locale] = self::cleanValue($value)
                ?: self::cleanValue($legacyValue)
                ?: ($locale === 'ka' ? self::cleanValue($fallback) : '');
        }

        return $values;
    }

    private static function modelTranslations(Model $model): array
    {
        $translations = $model->getAttribute('translations');

        return is_array($translations) ? $translations : [];
    }

    private static function mergeValues(array &$map, string $key, array $values): void
    {
        if (! isset($map[$key])) {
            $map[$key] = ['ka' => '', 'en' => '', 'ru' => ''];
        }

        foreach (self::LOCALES as $locale) {
            $value = self::cleanValue($values[$locale] ?? '');

            if ($value !== '') {
                $map[$key][$locale] = $value;
            }
        }
    }

    private static function localeValues(array $entry): array
    {
        return [
            'ka' => self::cleanValue($entry['ka'] ?? ''),
            'en' => self::cleanValue($entry['en'] ?? ''),
            'ru' => self::cleanValue($entry['ru'] ?? ''),
        ];
    }

    private static function cleanValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}
