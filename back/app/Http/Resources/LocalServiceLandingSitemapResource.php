<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocalServiceLandingSitemapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'locationSlug' => $this->location_slug,
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
