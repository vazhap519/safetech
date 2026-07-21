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
        $category = $this->resource->relationLoaded('category')
            ? $this->resource->getRelation('category')
            : null;
        $testimonial = $this->testimonial ?? [];
        if (array_is_list($testimonial)) {
            $testimonial = $testimonial[0] ?? [];
        }
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
            'id' => $this->id, 'slug' => $this->slug, 'name' => $name,
            'updated_at' => $this->updated_at?->toAtomString(),
            'title' => $title ?: $name, 'description' => $description,
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
            'icon' => $this->icon, 'accent' => $this->accent, 'meta' => $this->meta ?? [], 'scope' => $this->scope ?? [],
            'specs' => $this->specs ?? [], 'challenges' => $this->challenges ?? [],
            'solutions' => $this->solutions ?? [], 'process' => $this->process ?? [],
            'gallery' => $this->gallery_urls ?: ($this->gallery ?? []), 'results' => $this->results ?? [],
            'testimonial' => $testimonial, 'related' => $this->relatedProjects($request, $locale),
            'featured' => $this->is_featured, 'publishedAt' => $this->published_at?->toAtomString(),
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
            ->with(['category', 'media'])
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
                $category = $project->category;
                $categoryName = $category
                    ? $this->translatedModel($category, 'name', $category->name, $locale)
                    : $project->category_name;

                return [
                    'translationIndex' => $index,
                    'slug' => $project->slug,
                    'title' => filled($item['title'] ?? null) ? $item['title'] : $title,
                    'category' => filled($item['category'] ?? null) ? $item['category'] : $categoryName,
                    'image' => $project->thumb_url,
                    'imageAlt' => filled($item['imageAlt'] ?? null)
                        ? $item['imageAlt']
                        : ($project->image_alt ?: $title),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
