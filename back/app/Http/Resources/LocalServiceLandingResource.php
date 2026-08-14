<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalServiceLandingResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $service = $this->service;
        $title = $this->translated('title', $this->title, $locale);
        $excerpt = $this->translated('excerpt', $this->excerpt, $locale);
        $content = $this->translated('content', $this->content, $locale);
        $seoTitle = $this->translated('seoTitle', $this->seo_title ?: $title, $locale);
        $seoDescription = $this->translated(
            'seoDescription',
            $this->seo_description ?: ($excerpt ?: $content),
            $locale,
        );
        $localizedKeywords = data_get($this->translations, "keywords.{$locale}");
        $keywords = is_array($localizedKeywords)
            ? array_values(array_filter($localizedKeywords, 'is_string'))
            : ($this->keywords ?? []);

        return [
            'id' => $this->id,
            'locationSlug' => $this->location_slug,
            'locationName' => $this->translated('locationName', $this->location_name, $locale),
            'eyebrow' => $this->translated('eyebrow', $this->eyebrow, $locale),
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => $content,
            'benefits' => $this->localizedItems($this->benefits, ['title', 'description'], $locale),
            'faqs' => $this->localizedItems($this->faq, ['question', 'answer'], $locale),
            'ctaTitle' => $this->translated('ctaTitle', $this->cta_title, $locale),
            'ctaText' => $this->translated('ctaText', $this->cta_text, $locale),
            'primaryKeyword' => $this->translated(
                'primaryKeyword',
                $this->primary_keyword,
                $locale,
            ),
            'keywords' => $keywords,
            'service' => [
                'slug' => $service->slug,
                'name' => $this->translatedModel(
                    $service,
                    'name',
                    $service->name ?: $service->title,
                    $locale,
                ),
                'title' => $this->translatedModel(
                    $service,
                    'title',
                    $service->title ?: $service->name,
                    $locale,
                ),
                'heroImage' => $service->image,
            ],
            'projects' => $this->publicProjects
                ->map(fn (Project $project): array => [
                    'slug' => $project->slug,
                    'title' => $this->translatedModel(
                        $project,
                        'title',
                        $project->title ?: $project->name,
                        $locale,
                    ),
                    'description' => $this->translatedModel(
                        $project,
                        'description',
                        $project->description ?: $project->excerpt,
                        $locale,
                    ),
                    'image' => $project->thumb_url ?: $project->cover_url,
                ])
                ->values()
                ->all(),
            'seo' => [
                'title' => $seoTitle ?: $title,
                'description' => $seoDescription,
                'keywords' => $keywords,
                'image' => $service->social_image_url ?: $service->image,
                'noindex' => $this->noindex,
            ],
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $items
     * @param  array<int, string>  $fields
     * @return array<int, array<string, mixed>>
     */
    private function localizedItems(?array $items, array $fields, string $locale): array
    {
        return collect($items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(function (array $item) use ($fields, $locale): array {
                foreach ($fields as $field) {
                    $fallback = trim((string) ($item[$field] ?? ''));
                    $localized = trim((string) data_get(
                        $item,
                        "translations.{$locale}.{$field}",
                        '',
                    ));
                    $item[$field] = $localized !== '' ? $localized : $fallback;
                }

                unset($item['translations']);

                return $item;
            })
            ->filter(function (array $item) use ($fields): bool {
                return collect($fields)
                    ->contains(fn (string $field): bool => filled($item[$field] ?? null));
            })
            ->values()
            ->all();
    }
}
