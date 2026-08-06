<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $title = $this->translated('title', $this->title, $locale);
        $excerpt = $this->translated('excerpt', $this->excerpt, $locale);
        $content = $this->translated('content', $this->content, $locale);
        $seoTitle = $this->translated('seoTitle', $this->seo_title ?: $title, $locale);
        $seoDescription = $this->translated('seoDescription', $this->seo_description ?: ($excerpt ?: $content), $locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => $content,
            'coverImage' => $this->cover_image,
            'seo' => [
                'title' => $seoTitle ?: $title,
                'description' => $seoDescription,
                'keywords' => $this->keywords ?? [],
                'image' => $this->cover_image,
                'noindex' => $this->noindex,
            ],
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
