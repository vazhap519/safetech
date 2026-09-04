<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectCategory extends Model
{
    use FlushesPublicContentCache;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'sort_order',
        'seo',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'noindex',
        'translations',
    ];

    protected $casts = [
        'seo' => 'array',
        'seo_keywords' => 'array',
        'faq' => 'array',
        'schema' => 'array',
        'translations' => 'array',
        'noindex' => 'boolean',
    ];

    /* =========================
       🔗 RELATION
    ========================= */
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }

    /* =========================
       🔥 AUTO SLUG
    ========================= */
    protected static function booted()
    {
        static::saving(function (self $category): void {
            $translations = is_array($category->translations) ? $category->translations : [];

            data_set($translations, 'fields.name.ka', trim((string) $category->name));

            $category->translations = $translations;
        });

        static::creating(function ($category) {
            if (! $category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

    }
}
