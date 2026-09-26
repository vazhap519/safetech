<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSitemapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $category = $this->resource->relationLoaded('category')
            ? $this->resource->getRelation('category')
            : null;

        return [
            'slug' => $this->slug,
            'name' => $this->name ?: $this->title,
            'title' => $this->title ?: $this->name,
            'image' => $this->image,
            'category' => $category ? ['slug' => $category->slug] : null,
            'seo' => [
                'noindex' => (bool) data_get($this->seo, 'noindex', false),
            ],
            'indexable' => true,
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
