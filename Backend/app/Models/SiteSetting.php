<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
'site_name',
    // HomePage
        'our_services',
        'recent_projets',
        'client_reviews',
        'latest_articles',
        'get_consultation',

        // TopBar
        'top_bar_consultation_text',
        'top_bar_number',

        // Navigation
        'navigation_services',
        'navigation_projects',
        'navigation_about',
        'navigation_blog',
        'navigation_contact',

        // Footer
        'footer_services',
        'footer_contact',
        'footer_company',
        'footer_description',

        // Styles
        'styles',
    ];

    protected $casts = [
        'styles' => 'array', // JSON → array
    ];

    /*
    |--------------------------------------------------------------------------
    | Media Collections
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile()->useDisk('public');
        $this->addMediaCollection('favicon')->singleFile()->useDisk('public');
    }

    /*
    |--------------------------------------------------------------------------
    | Media Conversions (WebP 🔥)
    |--------------------------------------------------------------------------
    */

    public function registerMediaConversions(Media $media = null): void
    {
        // Logo WebP
        $this->addMediaConversion('logo_webp')
            ->format('webp')
        ->quality(80)
        ->nonQueued() // 🔥 აქ უნდა იყოს
        ->performOnCollections('logo');

        // Favicon WebP
        $this->addMediaConversion('favicon_webp')
             ->format('webp')
        ->quality(80)
        ->nonQueued()
        ->performOnCollections('favicon');
    }
}