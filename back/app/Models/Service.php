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

        'phone',
        'button_text',
        'features',
        'faq',
        'seo',
        'short_description',
         'description',
'long_description',
    ];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'seo' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | 📸 MEDIA COLLECTION
    |--------------------------------------------------------------------------
    */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('services')
            ->useDisk('public');
    }

    /*
    |--------------------------------------------------------------------------
    | 🖼 CONVERSIONS
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | 🧠 IMAGE ACCESSOR (SAFE FIX)
    |--------------------------------------------------------------------------
    */
    public function getImageUrlAttribute()
    {
        try {
            // თუ media არ არსებობს საერთოდ
            if (!$this->hasMedia('services')) {
                return null;
            }

            // ❗ არ ვიყენებთ webp-ს (conversion შეიძლება არ იყოს მზად)
            return $this->getFirstMediaUrl('services');

        } catch (\Throwable $e) {
            return null;
        }
    }


}
