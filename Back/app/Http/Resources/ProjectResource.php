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

        return [
            'id' => $this->id, 'slug' => $this->slug, 'name' => $this->name,
            'title' => $this->title, 'description' => $this->description,
            'seoDescription' => $this->seo_description, 'image' => $this->image,
            'imageAlt' => $this->image_alt, 'category' => $this->category, 'technology' => $this->technology,
            'icon' => $this->icon, 'accent' => $this->accent, 'meta' => $this->meta ?? [], 'scope' => $this->scope ?? [],
            'specs' => $this->specs ?? [], 'challenges' => $this->challenges ?? [],
            'solutions' => $this->solutions ?? [], 'process' => $this->process ?? [],
            'gallery' => $this->gallery ?? [], 'results' => $this->results ?? [],
            'testimonial' => $testimonial, 'related' => $this->related ?? [],
            'featured' => $this->is_featured, 'publishedAt' => $this->published_at?->toAtomString(),
        ];
    }
}
