<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
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

        'problems',
'results',
'testimonials',
'case_study',
'cta_title',
'cta_description',
    ];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'seo' => 'array',
        'problems'=>'array',
'results'=>'array',
'testimonials'=>'array',
'case_study'=>'array',
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
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

    /*
    |--------------------------------------------------------------------------
    | 🧠 IMAGE ACCESSOR (SAFE FIX)
    |--------------------------------------------------------------------------
    */
    public function getImageAttribute(): ?string
    {
        try {
            $media = $this->getFirstMedia('services'); // ✅

            if (!$media) return null;

            $url = $media->hasGeneratedConversion('webp')
                ? $media->getFullUrl('webp')
                : $media->getFullUrl();

            return str_replace(
                ['http://127.0.0.1:8000', 'http://localhost:8000'],
                config('app.url'),
                $url
            );

        } catch (\Throwable $e) {
            return null;
        }
    }


//    public function getImageUrlAttribute()
//    {
//        try {
//            // თუ media არ არსებობს საერთოდ
//            if (!$this->hasMedia('services')) {
//                return null;
//            }
//
//            // ❗ არ ვიყენებთ webp-ს (conversion შეიძლება არ იყოს მზად)
//            return $this->getFirstMediaUrl('services');
//
//        } catch (\Throwable $e) {
//            return null;
//        }
//    }


}
