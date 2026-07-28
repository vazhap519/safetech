<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use App\Models\ProductFilter;
use App\Support\SafeHtml;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $category = $this->resource->relationLoaded('category')
            ? $this->resource->getRelation('category')
            : null;
        $safeHtml = app(SafeHtml::class);
        $fallbackName = $this->name;
        $name = $this->translated('name', $fallbackName, $locale);
        $shortDescription = $safeHtml->sanitize(
            $this->translated('shortDescription', $this->short_description, $locale),
        );
        $description = $safeHtml->sanitize(
            $this->translated('description', $this->description, $locale),
        );
        $seoTitle = $this->translated(
            'seoTitle',
            data_get($this->seo, 'title', $fallbackName),
            $locale,
        );
        $seoDescription = $safeHtml->sanitize(
            $this->translated(
                'seoDescription',
                data_get($this->seo, 'description', $this->short_description),
                $locale,
            ),
        );
        $imageAlt = $this->translated('imageAlt', $this->image_alt ?: $fallbackName, $locale);

        return [
            'id' => $this->id,
            'updated_at' => $this->updated_at?->toAtomString(),
            'slug' => $this->slug,
            'name' => $name,
            'shortDescription' => $shortDescription,
            'description' => $description,
            'details' => $description,
            'image' => $this->image,
            'cardImage' => $this->card_image,
            'thumb' => $this->thumb_url,
            'imageAlt' => $imageAlt,
            'gallery' => $this->gallery_urls,
            'price' => $this->price !== null ? (float) $this->price : null,
            'currency' => $this->currency ?: 'GEL',
            'contactForPrice' => $this->price === null,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $category
                    ? $this->translatedModel($category, 'name', $category->name, $locale)
                    : null,
                'slug' => $category?->slug,
            ]),
            'filters' => $this->resolvedFilters($locale),
            'seo' => [
                'title' => $seoTitle ?: $name,
                'description' => $seoDescription ?: strip_tags($shortDescription),
                'keywords' => $this->stringList(data_get($this->seo, 'keywords', [])),
                'image' => data_get($this->seo, 'image', $this->image),
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'canonical' => data_get($this->seo, 'canonical'),
                'ogTitle' => $this->translated(
                    'ogTitle',
                    data_get($this->seo, 'og_title', data_get($this->seo, 'title', $name)),
                    $locale,
                ),
                'ogDescription' => strip_tags($this->translated(
                    'ogDescription',
                    data_get($this->seo, 'og_description', data_get($this->seo, 'description', $shortDescription)),
                    $locale,
                )),
                'schema' => data_get($this->seo, 'schema'),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function resolvedFilters(string $locale): array
    {
        $filterAssignments = $this->resource->normalizedFilterValues();

        if ($filterAssignments->isEmpty()) {
            return [];
        }

        $filters = ProductFilter::query()
            ->whereIn('slug', $filterAssignments->pluck('filter_slug')->all())
            ->orderBy('sort_order')
            ->get()
            ->keyBy('slug');

        return $filterAssignments
            ->map(function (array $assignment) use ($filters, $locale): ?array {
                /** @var ProductFilter|null $filter */
                $filter = $filters->get($assignment['filter_slug']);

                if (! $filter) {
                    return null;
                }

                $localizedName = $this->translatedModel($filter, 'name', $filter->name, $locale);
                $resolvedOptions = $filter->resolvedOptions()->keyBy('slug');
                $options = collect($assignment['option_slugs'])
                    ->map(function (string $slug) use ($resolvedOptions, $locale): ?array {
                        $option = $resolvedOptions->get($slug);

                        if (! is_array($option)) {
                            return null;
                        }

                        $translations = is_array($option['translations'] ?? null)
                            ? $option['translations']
                            : [];
                        $label = is_string($translations[$locale] ?? null) && trim((string) $translations[$locale]) !== ''
                            ? trim((string) $translations[$locale])
                            : $option['label'];

                        return [
                            'slug' => $option['slug'],
                            'name' => $label,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                if ($options === []) {
                    return null;
                }

                return [
                    'slug' => $filter->slug,
                    'name' => $localizedName,
                    'options' => $options,
                ];
            })
            ->filter()
            ->values()
            ->all();
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
            ->values()
            ->all();
    }
}
