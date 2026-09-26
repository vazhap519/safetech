<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalServiceLandingSummaryResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $service = $this->service;
        $title = $this->translated('title', $this->title, $locale);

        return [
            'id' => $this->id,
            'locationSlug' => $this->location_slug,
            'locationName' => $this->translated('locationName', $this->location_name, $locale),
            'title' => $title,
            'service' => [
                'slug' => $service->slug,
                'name' => $this->translatedModel(
                    $service,
                    'name',
                    $service->name ?: $service->title,
                    $locale,
                ),
                'title' => $this->translatedModel(
                    $service,
                    'title',
                    $service->title ?: $service->name,
                    $locale,
                ),
            ],
            'projects' => $this->publicProjects
                ->map(fn ($project): array => ['slug' => $project->slug])
                ->values()
                ->all(),
            'seo' => [
                'title' => $this->translated('seoTitle', $this->seo_title ?: $title, $locale),
                'noindex' => (bool) $this->noindex,
            ],
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
