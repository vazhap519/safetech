<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $category = $this->resource->relationLoaded('projectCategory')
            ? $this->resource->getRelation('projectCategory')
            : ($this->resource->relationLoaded('category') ? $this->resource->getRelation('category') : null);
        $fallbackName = $this->name ?: $this->title;
        $name = $this->translated('name', $fallbackName, $locale);
        $title = $this->translated('title', $this->title ?: $fallbackName, $locale);
        $description = $this->translated(
            'description',
            $this->description ?: ($this->excerpt ?: $this->content),
            $locale,
        );
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
        $imageAlt = $this->translated('imageAlt', $this->image_alt ?: $this->title ?: $fallbackName, $locale);
        $technology = $this->translated('technology', $this->technology, $locale);
        $image = $this->cover_url ?: $this->image;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $name,
            'updated_at' => $this->updated_at?->toAtomString(),
            'title' => $title ?: $name,
            'description' => $description,
            'seoDescription' => $seoDescription,
            'image' => $image,
            'imageAlt' => $imageAlt,
            'videoUrl' => $this->video_url,
            'video_url' => $this->video_url,
            'category' => $this->category_slug,
            'categoryName' => $category
                ? $this->translatedModel($category, 'name', $category->name, $locale)
                : $this->category_name,
            'technology' => $technology,
            'icon' => $this->icon,
            'accent' => $this->accent,
            'meta' => $this->localizedValueLabelItems($this->meta ?? [], 'meta', $locale),
            'scope' => $this->localizedValueLabelItems($this->scope ?? [], 'scope', $locale),
            'specs' => $this->localizedValueLabelItems($this->specs ?? [], 'spec', $locale),
            'challenges' => $this->localizedDetailCards($this->challenges ?? [], 'challenge', $locale),
            'solutions' => $this->localizedDetailCards($this->solutions ?? [], 'solution', $locale),
            'process' => $this->localizedProcess($this->process ?? [], $locale),
            'gallery' => $this->localizedGallery($this->gallery_urls ?: ($this->gallery ?? []), $locale),
            'results' => $this->localizedResults($this->results ?? [], $locale),
            'related' => $this->relatedProjects($request, $locale),
            'featured' => $this->is_featured,
            'publishedAt' => $this->published_at?->toAtomString(),
            'seo' => [
                'title' => $seoTitle ?: $title,
                'description' => $seoDescription ?: $description,
                'keywords' => data_get($this->seo, 'keywords', []),
                'image' => data_get($this->seo, 'image', $image),
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'schema' => data_get($this->seo, 'schema'),
            ],
        ];
    }

    private function localizedValueLabelItems(array $items, string $prefix, string $locale): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item))
            ->values()
            ->map(fn (array $item, int $index): array => [
                ...$item,
                'value' => $this->localizedItemValue(
                    $item,
                    'value',
                    "{$prefix}.{$index}.value",
                    $locale,
                ),
                'label' => $this->localizedItemValue(
                    $item,
                    'label',
                    "{$prefix}.{$index}.label",
                    $locale,
                ),
            ])
            ->all();
    }

    private function localizedDetailCards(array $items, string $prefix, string $locale): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item))
            ->values()
            ->map(fn (array $item, int $index): array => [
                ...$item,
                'title' => $this->localizedItemValue(
                    $item,
                    'title',
                    "{$prefix}.{$index}.title",
                    $locale,
                ),
                'description' => $this->localizedItemValue(
                    $item,
                    'description',
                    "{$prefix}.{$index}.description",
                    $locale,
                ),
            ])
            ->all();
    }

    private function localizedProcess(array $items, string $locale): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item))
            ->values()
            ->map(fn (array $item, int $index): array => [
                ...$item,
                'title' => $this->localizedItemValue(
                    $item,
                    'title',
                    "process.{$index}.title",
                    $locale,
                ),
                'description' => $this->localizedItemValue(
                    $item,
                    'description',
                    "process.{$index}.description",
                    $locale,
                ),
            ])
            ->all();
    }

    private function localizedGallery(array $items, string $locale): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item) && filled($item['src'] ?? null))
            ->values()
            ->map(fn (array $item, int $index): array => [
                ...$item,
                'alt' => $this->translatedEntry(
                    $this->resource,
                    "gallery.{$index}.alt",
                    $item['alt'] ?? $this->image_alt ?? $this->title ?? $this->name ?? '',
                    $locale,
                ),
            ])
            ->all();
    }

    private function localizedResults(array $items, string $locale): array
    {
        return collect($items)
            ->filter(fn ($item): bool => is_array($item))
            ->values()
            ->map(fn (array $item, int $index): array => [
                ...$item,
                'value' => $this->localizedItemValue(
                    $item,
                    'value',
                    "result.{$index}.value",
                    $locale,
                ),
                'title' => $this->localizedItemValue(
                    $item,
                    'title',
                    "result.{$index}.title",
                    $locale,
                ),
                'description' => $this->localizedItemValue(
                    $item,
                    'description',
                    "result.{$index}.description",
                    $locale,
                ),
            ])
            ->all();
    }

    private function localizedItemValue(
        array $item,
        string $field,
        string $legacyKey,
        string $locale,
        mixed $fallback = null,
    ): mixed {
        $baseValue = $fallback ?? ($item[$field] ?? '');

        if ($locale !== 'ka') {
            $nestedTranslation = data_get($item, "translations.{$locale}.{$field}");

            if (is_string($nestedTranslation) && trim($nestedTranslation) !== '') {
                return $nestedTranslation;
            }

            if (! is_string($nestedTranslation) && $nestedTranslation !== null && $nestedTranslation !== '') {
                return $nestedTranslation;
            }
        }

        return $this->translatedEntry(
            $this->resource,
            $legacyKey,
            $baseValue,
            $locale,
        );
    }

    private function relatedProjects(Request $request, string $locale): array
    {
        if (! $request->routeIs('api.projects.show')) {
            return [];
        }

        $configured = collect($this->related ?? [])
            ->filter(fn ($item): bool => is_array($item) && filled($item['slug'] ?? null))
            ->values();

        if ($configured->isEmpty()) {
            return [];
        }

        $projects = Project::query()
            ->publiclyVisible()
            ->whereKeyNot($this->id)
            ->whereIn('slug', $configured->pluck('slug')->all())
            ->with(['projectCategory', 'media'])
            ->get()
            ->keyBy('slug');

        return $configured
            ->map(function (array $item, int $index) use ($locale, $projects): ?array {
                /** @var Project|null $project */
                $project = $projects->get($item['slug']);

                if (! $project) {
                    return null;
                }

                $title = $this->translatedModel(
                    $project,
                    'title',
                    $project->title ?: $project->name,
                    $locale,
                );
                $category = $project->relationLoaded('projectCategory')
                    ? $project->getRelation('projectCategory')
                    : ($project->relationLoaded('category') ? $project->getRelation('category') : null);
                $categoryName = $category
                    ? $this->translatedModel($category, 'name', $category->name, $locale)
                    : $project->category_name;

                return [
                    'translationIndex' => $index,
                    'slug' => $project->slug,
                    'title' => $this->localizedItemValue(
                        $item,
                        'title',
                        "related.{$index}.title",
                        $locale,
                        filled($item['title'] ?? null) ? $item['title'] : $title,
                    ),
                    'category' => $this->localizedItemValue(
                        $item,
                        'category',
                        "related.{$index}.category",
                        $locale,
                        filled($item['category'] ?? null) ? $item['category'] : $categoryName,
                    ),
                    'image' => $project->thumb_url,
                    'imageAlt' => $this->localizedItemValue(
                        $item,
                        'imageAlt',
                        "related.{$index}.imageAlt",
                        $locale,
                        filled($item['imageAlt'] ?? null)
                            ? $item['imageAlt']
                            : ($project->image_alt ?: $title),
                    ),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
