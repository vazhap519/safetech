<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeHeroSection extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'home_hero_title',
        'home_hero_description',
        'home_hero_list',
        'home_hero_call_button_text',
        'home_hero_call_button_number',
        'home_hero_service_button_text',
        'image_url'
    ];

    protected $casts = [
        'home_hero_list' => 'array',
    ];

    /**
     * ✅ Media Collection
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('hero')
            ->useDisk('public');
    }

    /**
     * ✅ Image Optimization
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
     * ✅ Clean accessor (no controller logic)
     */
    public function getImageUrlAttribute()
    {
        $url = $this->getFirstMediaUrl('hero', 'webp');

        return $url ? url($url) : null;
    }
    /**
     * ✅ Auto formatted phone
     */
    public function getFormattedPhoneAttribute()
    {
        $digits = preg_replace('/\D/', '', $this->home_hero_call_button_number);

        return preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $digits);
    }
}
