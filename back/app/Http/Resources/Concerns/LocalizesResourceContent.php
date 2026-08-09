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
        $entries = is_array($translations) ? Arr::get($translations, 'entries', []) : [];
        $acceptedKeys = $this->translationEntryKeys($key);

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
