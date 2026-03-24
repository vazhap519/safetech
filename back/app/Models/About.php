<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class About extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'hero_title',
        'hero_description',
        'hero_trust_list',
        'hero_badge',

        'story_title',
        'story_title_description',
        'story_content',
        'story_stats',

        'why_us_title',
        'why_us_title_description',
        'why_us_content',

        'cta_title',
        'cta_title_description',
        'cta_trust',
        'cta_phone'
    ];

    protected $casts = [
        'hero_trust_list' => 'array',
        'story_stats' => 'array',
        'why_us_content' => 'array',
        'cta_trust' => 'array',
    ];

    /**
     * MEDIA COLLECTION
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('hero')
            ->singleFile();
    }

    /**
     * IMAGE OPTIMIZATION
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('webp')
            ->format('webp')
            ->width(800) // 🔥 დაამატე ზომა
            ->quality(75)
            ->nonQueued();
    }

    /**
     * ✅ CLEAN ACCESSOR
     */
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('hero', 'webp') ?: null;
    }
}
