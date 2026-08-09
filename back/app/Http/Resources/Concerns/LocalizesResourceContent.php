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

        if (is_array($entries)) {
            foreach ($entries as $entry) {
                if (! is_array($entry) || trim((string) ($entry['key'] ?? '')) !== $key) {
                    continue;
                }

                $value = trim((string) ($entry[$locale] ?? ''));

                if ($value !== '') {
                    return $value;
                }

                break;
            }
        }

        return is_string($fallback) ? $fallback : '';
    }
}
