<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $category = $this->resource->relationLoaded('category')
            ? $this->resource->getRelation('category')
            : null;
        $fallbackName = $this->name ?: $this->title;
        $name = $this->translated('name', $fallbackName, $locale);
        $title = $this->translated('title', $this->title ?: $fallbackName, $locale);
        $description = $this->translated(
            'description',
            $this->description ?: ($this->short_description ?: $this->long_description),
            $locale,
        );
        $eyebrow = $this->translated('eyebrow', $this->eyebrow, $locale);
        $seoTitle = $this->translated(
            'seoTitle',
            data_get($this->seo, 'title', $this->title ?: $fallbackName),
            $locale,
        );
        $seoDescription = $this->translated(
            'seoDescription',
            $this->seo_description ?: data_get($this->seo, 'description', $description),
            $locale,
        );
        $ogTitle = $this->translated('ogTitle', $seoTitle ?: $title, $locale);
        $ogDescription = $this->translated('ogDescription', $seoDescription ?: $description, $locale);
        $keywords = $this->translatedStringArray('keywords', $this->keywords ?? [], $locale);
        $highlights = $this->translatedStringArray('highlights', $this->highlights ?? [], $locale);
        $industries = $this->translatedStringArray('industries', $this->industries ?? [], $locale);
        $configuredSeoImage = trim((string) data_get($this->seo, 'image', ''));
        $socialImage = $configuredSeoImage !== ''
            ? $configuredSeoImage
            : $this->social_image_url;
        $faqs = $this->relationLoaded('faqs')
            ? $this->faqs->map(fn ($faq) => [
                'question' => $this->translatedModel($faq, 'question', $faq->question, $locale),
                'answer' => $this->translatedModel($faq, 'answer', $faq->answer, $locale),
            ])->values()->all()
            : collect($this->faq ?? [])->map(fn ($faq) => [
                'question' => $faq['question'] ?? $faq['q'] ?? '',
                'answer' => $faq['answer'] ?? $faq['a'] ?? '',
            ])->filter(fn ($faq) => $faq['question'] || $faq['answer'])->values()->all();

        return [
            'id' => $this->id,
            'updated_at' => $this->updated_at?->toAtomString(),
            'slug' => $this->slug,
            'name' => $name,
            'eyebrow' => $eyebrow,
            'icon' => $this->icon,
            'title' => $title ?: $name,
            'description' => $description,
            'shortDescription' => $this->short_description ?: $description,
            'longDescription' => $this->long_description ?: $description,
            'seoDescription' => $seoDescription,
            'heroImage' => $this->image,
            'socialImage' => $socialImage,
            'image' => $this->image,
            'keywords' => $keywords,
            'highlights' => $highlights,
            'overview' => $this->overview ?: [
                'title' => $this->title ?: $name,
                'paragraphs' => array_values(array_filter([$description])),
                'stats' => [],
            ],
            'benefits' => $this->localizedItems('benefit', $this->benefits ?? [], $locale),
            'solutions' => $this->localizedItems('solution', $this->solutions ?? [], $locale),
            'industries' => $industries,
            'process' => $this->localizedItems('process', $this->process ?? [], $locale),
            'brands' => $this->brands ?? [],
            'features' => $this->features ?? [],
            'warranty' => $this->translated('warranty', $this->warranty, $locale),
            'sla' => $this->translated('sla', $this->sla, $locale),
            'leadForm' => $this->lead_form ?? null,
            'faqs' => $faqs,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $category
                    ? $this->translatedModel($category, 'name', $category->name, $locale)
                    : null,
                'slug' => $category?->slug,
            ]),
            'seo' => [
                'title' => $seoTitle ?: $title,
                'description' => $seoDescription ?: $description,
                'keywords' => $keywords,
                'image' => $socialImage,
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'canonical' => data_get($this->seo, 'canonical'),
                'schemaType' => data_get($this->seo, 'schema_type', 'Service'),
                'og' => ['title' => $ogTitle, 'description' => $ogDescription],
                'schema' => data_get($this->seo, 'schema'),
            ],
            'related' => [],
        ];
    }

    /** @param array<int, mixed> $fallback */
    private function translatedStringArray(string $field, array $fallback, string $locale): array
    {
        $translated = data_get($this->translations, "{$field}.{$locale}");

        if (! is_array($translated) || $translated === []) {
            return array_values(array_filter($fallback, fn (mixed $value): bool => is_string($value) && trim($value) !== ''));
        }

        return array_values(array_filter(
            array_map(fn (mixed $value): string => trim((string) $value), $translated),
            fn (string $value): bool => $value !== '',
        ));
    }

    /** @param array<int, mixed> $items */
    private function localizedItems(string $root, array $items, string $locale): array
    {
        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values()
            ->map(function (array $item, int $index) use ($root, $locale): array {
                foreach (['title', 'description'] as $field) {
                    $fallback = $item[$field] ?? '';
                    $nested = data_get($item, "translations.{$locale}.{$field}");
                    $item[$field] = is_string($nested) && trim($nested) !== ''
                        ? trim($nested)
                        : $this->translatedEntry($this->resource, "{$root}.{$index}.{$field}", $fallback, $locale);
                }

                unset($item['translations']);

                return $item;
            })
            ->all();
    }
}
