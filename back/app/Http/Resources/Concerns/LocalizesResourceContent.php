<?php

namespace App\Http\Resources\Concerns;

use App\Support\MultilingualContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait LocalizesResourceContent
{
    private function locale(Request $request): string
    {
        $locale = $request->string('locale')->toString();

        if (! in_array($locale, MultilingualContent::LOCALES, true)) {
            $locale = trim((string) $request->header('X-Safetech-Locale', ''));
        }

        return in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
    }

    private function translated(string $field, mixed $fallback, string $locale): string
    {
        return $this->translatedModel($this->resource, $field, $fallback, $locale);
    }

    private function translatedModel(Model $model, string $field, mixed $fallback, string $locale): string
    {
        $values = MultilingualContent::valuesForField($model, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }

    private function translatedEntry(Model $model, string $key, mixed $fallback, string $locale): string
    {
        $translations = $model->getAttribute('translations');
        $translations = is_array($translations) ? $translations : [];
        $acceptedKeys = $this->translationEntryKeys($key);

        foreach ($acceptedKeys as $acceptedKey) {
            $directValue = $this->directTranslationValue($translations, $acceptedKey, $locale);

            if ($directValue !== null) {
                return $directValue;
            }
        }

        $entries = Arr::get($translations, 'entries', []);

        if (is_array($entries)) {
            foreach ($entries as $entry) {
                if (! is_array($entry) || ! in_array(trim((string) ($entry['key'] ?? '')), $acceptedKeys, true)) {
                    continue;
                }

                $value = trim((string) ($entry[$locale] ?? ''));

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return is_string($fallback) ? $fallback : '';
    }

    /**
     * Resolve historical direct/nested translation layouts in addition to
     * translations.entries. This keeps content created by earlier admin forms
     * readable without rewriting or migrating production data.
     */
    private function directTranslationValue(array $translations, string $key, string $locale): ?string
    {
        $candidates = [
            data_get($translations, "fields.{$key}.{$locale}"),
            data_get($translations, "fields.{$locale}.{$key}"),
            data_get($translations, "{$key}.{$locale}"),
            data_get($translations, "{$locale}.{$key}"),
            $translations['fields'][$key][$locale] ?? null,
            $translations['fields'][$locale][$key] ?? null,
            $translations[$key][$locale] ?? null,
            $translations[$locale][$key] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate)) {
                $candidate = trim($candidate);

                if ($candidate !== '') {
                    return $candidate;
                }

                continue;
            }

            if ($candidate !== null && $candidate !== '') {
                return (string) $candidate;
            }
        }

        return null;
    }

    /**
     * Project repeaters have historically used singular translation keys
     * (challenge.0.title, result.0.description), while their stored model
     * attributes and admin field names are plural (challenges, results).
     * Accept both forms so existing and newly-entered translations resolve.
     *
     * @return array<int, string>
     */
    private function translationEntryKeys(string $key): array
    {
        $aliases = [
            'spec' => 'specs',
            'specs' => 'spec',
            'challenge' => 'challenges',
            'challenges' => 'challenge',
            'solution' => 'solutions',
            'solutions' => 'solution',
            'result' => 'results',
            'results' => 'result',
        ];

        $parts = explode('.', $key);
        $root = $parts[0] ?? '';
        $keys = [$key];

        if (isset($aliases[$root])) {
            $parts[0] = $aliases[$root];
            $keys[] = implode('.', $parts);
        }

        return array_values(array_unique($keys));
    }
}
