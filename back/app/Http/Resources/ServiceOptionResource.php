<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\LocalizesResourceContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceOptionResource extends JsonResource
{
    use LocalizesResourceContent;

    public function toArray(Request $request): array
    {
        $locale = $this->locale($request);
        $fallback = $this->name ?: $this->title ?: $this->slug;

        return [
            'slug' => $this->slug,
            'label' => $this->translated('name', $fallback, $locale),
        ];
    }
}
