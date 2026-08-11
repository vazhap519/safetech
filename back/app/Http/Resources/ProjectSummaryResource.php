<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProjectSummaryResource extends ProjectResource
{
    public function toArray(Request $request): array
    {
        return Arr::only(parent::toArray($request), [
            'id',
            'slug',
            'name',
            'updated_at',
            'title',
            'description',
            'image',
            'imageAlt',
            'videoUrl',
            'video_url',
            'category',
            'categoryName',
            'technology',
            'icon',
            'accent',
            'meta',
            'specs',
            'featured',
            'publishedAt',
        ]);
    }
}
