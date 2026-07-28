<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'sort_order',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'translations',
        'noindex',
    ];

    protected $casts = [
        'seo_keywords' => 'array',
        'faq' => 'array',
        'schema' => 'array',
        'translations' => 'array',
        'noindex' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductCategory $category): void {
            if (filled($category->slug)) {
                return;
            }

            $baseSlug = Str::slug($category->name ?: 'product-category') ?: 'product-category';
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $category->slug = $candidate;
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }
}
