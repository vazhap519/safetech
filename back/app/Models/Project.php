<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use FlushesPublicContentCache;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'meta' => 'array', 'scope' => 'array', 'specs' => 'array', 'challenges' => 'array',
            'solutions' => 'array', 'process' => 'array', 'gallery' => 'array', 'results' => 'array',
            'testimonial' => 'array', 'related' => 'array', 'is_featured' => 'boolean',
            'seo' => 'array', 'translations' => 'array', 'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
