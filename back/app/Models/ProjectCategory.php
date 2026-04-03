<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
        'seo',
    ];

    protected $casts = [
        'seo' => 'array',
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
        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
