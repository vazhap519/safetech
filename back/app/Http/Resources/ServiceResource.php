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
            'image' => $this->image,
            'keywords' => $this->keywords ?? [],
            'highlights' => $this->highlights ?? [],
            'overview' => $this->overview ?: [
                'title' => $this->title ?: $name,
                'paragraphs' => array_values(array_filter([$description])),
                'stats' => [],
            ],
            'benefits' => $this->benefits ?? [],
            'solutions' => $this->solutions ?? [],
            'industries' => $this->industries ?? [],
            'process' => $this->process ?? [],
            'brands' => $this->brands ?? [],
            'features' => $this->features ?? [],
            'warranty' => $this->warranty,
            'sla' => $this->sla,
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
                'keywords' => data_get($this->seo, 'keywords', $this->keywords ?? []),
                'image' => data_get($this->seo, 'image', $this->image),
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'schema' => data_get($this->seo, 'schema'),
            ],
            'related' => [],
        ];
    }
}
