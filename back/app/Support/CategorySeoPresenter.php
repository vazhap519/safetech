<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class CategorySeoPresenter
{
    public function __construct(private readonly SafeHtml $safeHtml) {}

    /** @return array<string, mixed> */
    public function present(Model $category, string $locale): array
    {
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
        $keywords = data_get($category->translations, "keywords.{$locale}");
        $faq = data_get($category->translations, "faq.{$locale}");
        $schema = data_get($category->translations, "schema.{$locale}");

        return [
            'id' => $category->getKey(),
            'name' => $this->translated($category, 'name', $category->getAttribute('name'), $locale),
            'slug' => $category->getAttribute('slug'),
            'seo_title' => $this->translated($category, 'seo_title', $category->getAttribute('seo_title'), $locale),
            'seo_description' => $this->translated($category, 'seo_description', $category->getAttribute('seo_description'), $locale),
            'seo_keywords' => $this->stringList(
                is_array($keywords) ? $keywords : $category->getAttribute('seo_keywords'),
            ),
            'intro_text' => $this->safeHtml->sanitize(
                $this->translated($category, 'intro_text', $category->getAttribute('intro_text'), $locale),
            ),
            'faq' => is_array($faq) ? $faq : ($category->getAttribute('faq') ?? []),
            'schema' => is_array($schema) ? $schema : $category->getAttribute('schema'),
            'noindex' => (bool) $category->getAttribute('noindex'),
            'updated_at' => $category->getAttribute('updated_at')?->toAtomString(),
        ];
    }

    private function translated(Model $model, string $field, mixed $fallback, string $locale): string
    {
        $values = MultilingualContent::valuesForField($model, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }

    /** @return array<int, string> */
    private function stringList(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->map(fn (mixed $value): mixed => is_array($value) ? ($value['value'] ?? null) : $value)
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->unique()
            ->values()
            ->all();
    }
}
