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
        $ogTitle = $this->translated('ogTitle', $seoTitle ?: $title, $locale);
        $ogDescription = $this->translated('ogDescription', $seoDescription ?: ($excerpt ?: $content), $locale);
        $localizedKeywords = data_get($this->translations, "keywords.{$locale}");
        $keywords = is_array($localizedKeywords) && $localizedKeywords !== []
            ? array_values(array_filter($localizedKeywords, fn (mixed $keyword): bool => is_string($keyword) && trim($keyword) !== ''))
            : ($this->keywords ?? []);

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
                'keywords' => $keywords,
                'image' => data_get($this->translations, 'seo.image', $this->cover_image),
                'noindex' => $this->noindex,
                'canonical' => data_get($this->translations, 'seo.canonical'),
                'schemaType' => data_get($this->translations, 'seo.schema_type', 'WebPage'),
                'og' => ['title' => $ogTitle, 'description' => $ogDescription],
                'schema' => $this->schema,
            ],
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
