<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'phone',
        'button_text',
        'features',
        'faq',
        'seo_text',
    ];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'seo_text' => 'array',
        ];

    /**
     * Media Collection
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('services')
            ->useDisk('public');
    }

    /**
     * Image Optimization
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('webp')
            ->format('webp')
            ->width(600)
            ->quality(70)
            ->nonQueued();
    }

    /**
     * Image accessor
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('services', 'webp') ?: null;
    }

    /**
     * 🔥 SEO accessor (მთავარი FIX)
     */
    public function getSeoAttribute()
    {
        $seo = $this->seo_text;

        if (is_array($seo)) {
            return $seo;
        }

        if (is_string($seo)) {
            return array_filter(array_map('trim', explode("\n", $seo)));
        }

        return [];
    }
}
