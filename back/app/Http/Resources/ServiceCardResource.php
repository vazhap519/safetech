<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ServiceCardResource extends ServiceResource
{
    public function toArray(Request $request): array
    {
        return Arr::only(parent::toArray($request), [
            'slug',
            'name',
            'title',
            'description',
            'icon',
            'category',
        ]);
    }
}
