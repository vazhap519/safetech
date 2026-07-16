<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $testimonial = $this->testimonial ?? [];
        if (array_is_list($testimonial)) $testimonial = $testimonial[0] ?? [];
        $name = $this->name ?: $this->title;
        $description = $this->description ?: ($this->excerpt ?: $this->content);
        $image = $this->cover_url ?: $this->image;

        return [
            'id' => $this->id, 'slug' => $this->slug, 'name' => $name,
            'title' => $this->title ?: $name, 'description' => $description,
            'seoDescription' => $this->seo_description ?: data_get($this->seo, 'description', $description),
            'image' => $image,
            'imageAlt' => $this->image_alt ?: $this->title ?: $name,
            'category' => $this->category_slug,
            'categoryName' => $this->category_name,
            'technology' => $this->technology,
            'icon' => $this->icon, 'accent' => $this->accent, 'meta' => $this->meta ?? [], 'scope' => $this->scope ?? [],
            'specs' => $this->specs ?? [], 'challenges' => $this->challenges ?? [],
            'solutions' => $this->solutions ?? [], 'process' => $this->process ?? [],
            'gallery' => $this->gallery_urls ?: ($this->gallery ?? []), 'results' => $this->results ?? [],
            'testimonial' => $testimonial, 'related' => $this->related ?? [],
            'featured' => $this->is_featured, 'publishedAt' => $this->published_at?->toAtomString(),
            'seo' => [
                'title' => data_get($this->seo, 'title', $this->title ?: $name),
                'description' => data_get($this->seo, 'description', $this->seo_description ?: $description),
                'keywords' => data_get($this->seo, 'keywords', []),
                'image' => data_get($this->seo, 'image', $image),
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
                'schema' => data_get($this->seo, 'schema'),
            ],
        ];
    }
}
