<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryForService extends Model
{
    use FlushesPublicContentCache, HasFactory;

    protected $fillable = [
        'name',
        'slug',
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
        'seo_keywords' => 'array',
        'faq' => 'array',
        'schema' => 'array',
        'translations' => 'array',
        'noindex' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (! $category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

    }

    /*
    |------------------------------------------------------------------
    | 🔗 RELATION
    |------------------------------------------------------------------
    */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_for_service_id');
    }
}
