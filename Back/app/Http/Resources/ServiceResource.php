<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'eyebrow' => $this->eyebrow,
            'icon' => $this->icon,
            'title' => $this->title,
            'description' => $this->description,
            'seoDescription' => $this->seo_description,
            'heroImage' => $this->hero_image,
            'keywords' => $this->keywords ?? [],
            'highlights' => $this->highlights ?? [],
            'overview' => $this->overview ?? [],
            'benefits' => $this->benefits ?? [],
            'solutions' => $this->solutions ?? [],
            'industries' => $this->industries ?? [],
            'process' => $this->process ?? [],
            'brands' => $this->brands ?? [],
            'warranty' => $this->warranty,
            'sla' => $this->sla,
            'leadForm' => $this->lead_form ?? null,
            'faqs' => $this->whenLoaded('faqs', fn () => $this->faqs->map(fn ($faq) => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])),
            'related' => [],
        ];
    }
}
