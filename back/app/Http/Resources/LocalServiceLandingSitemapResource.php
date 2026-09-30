<?php

namespace App\Http\Resources;

use App\Support\MultilingualContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalServiceLandingSitemapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $availableLocales = ['ka'];

        foreach (['en', 'ru'] as $locale) {
            $requiredFields = [
                'locationName' => $this->location_name,
                'title' => $this->title,
                'content' => $this->content,
                'seoTitle' => $this->seo_title ?: $this->title,
                'seoDescription' => $this->seo_description ?: ($this->excerpt ?: $this->content),
            ];
            $complete = true;

            foreach ($requiredFields as $field => $fallback) {
                $values = MultilingualContent::valuesForField($this->resource, $field, $fallback);

                if (blank($values[$locale] ?? null)) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                $availableLocales[] = $locale;
            }
        }

        return [
            'locationSlug' => $this->location_slug,
            'availableLocales' => $availableLocales,
            'service' => [
                'slug' => $this->service->slug,
            ],
            'seo' => [
                'noindex' => (bool) $this->noindex,
            ],
            'indexable' => true,
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
