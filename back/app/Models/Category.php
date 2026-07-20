<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use FlushesPublicContentCache;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'seo_keywords' => 'array',
            'faq' => 'array',
            'schema' => 'array',
            'noindex' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (! $category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
