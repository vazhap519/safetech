<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCardResource extends JsonResource
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

        return [
            'slug' => $this->slug,
            'name' => $name,
            'title' => $title ?: $name,
            'description' => $description,
            'icon' => $this->icon,
            'category' => $category ? [
                'name' => $this->translatedModel($category, 'name', $category->name, $locale),
                'slug' => $category->slug,
            ] : null,
        ];
    }
}
