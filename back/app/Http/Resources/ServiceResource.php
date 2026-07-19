<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $name = $this->name ?: $this->title;
        $description = $this->description ?: ($this->short_description ?: $this->long_description);
        $seoDescription = $this->seo_description ?: data_get($this->seo, 'description', $description);
        $faqs = $this->relationLoaded('faqs')
            ? $this->faqs->map(fn ($faq) => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])->values()
            : collect($this->faq ?? [])->map(fn ($faq) => [
                'question' => $faq['question'] ?? $faq['q'] ?? '',
                'answer' => $faq['answer'] ?? $faq['a'] ?? '',
            ])->filter(fn ($faq) => $faq['question'] || $faq['answer'])->values();

        return [
            'id' => $this->id,
            'updated_at' => $this->updated_at?->toAtomString(),
            'slug' => $this->slug,
            'name' => $name,
            'eyebrow' => $this->eyebrow,
            'icon' => $this->icon,
            'title' => $this->title ?: $name,
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
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ]),
            'seo' => [
                'title' => data_get($this->seo, 'title', $this->title ?: $name),
                'description' => data_get($this->seo, 'description', $seoDescription),
                'keywords' => data_get($this->seo, 'keywords', $this->keywords ?? []),
                'image' => data_get($this->seo, 'image', $this->image),
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'schema' => data_get($this->seo, 'schema'),
            ],
            'related' => [],
        ];
    }
}
