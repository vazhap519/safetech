<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use FlushesPublicContentCache;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'keywords' => 'array', 'highlights' => 'array', 'overview' => 'array',
            'benefits' => 'array', 'solutions' => 'array', 'industries' => 'array',
            'process' => 'array', 'brands' => 'array', 'is_published' => 'boolean',
        ];
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
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
