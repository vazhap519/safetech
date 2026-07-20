<?php

namespace App\Http\Resources\Concerns;

use App\Support\MultilingualContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
}
