<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class Setting extends Model   implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'share_title',
        'share_buttons',
/*
 * ============================================================================================
 *                      FOOTER
 * ============================================================================================
 */
        /*
 * ============================================================================================
 *                      FOOTER BRAND
 * ============================================================================================
 */
        'footer_brand_text',
        'footer_brand_soc',

        /*
 * ============================================================================================
 *                      FOOTER HEADERS
 * ============================================================================================
 */
        'footer_headers',
        /*
 * ============================================================================================
 *                      FOOTER CONTACT AREA
 * ============================================================================================
 */
        'footer_contact_area',
        /*
    * ============================================================================================
    *                      FOOTER COPYRIGHT
    * ============================================================================================
    */
        'footer_copyright_text',



        'seo',

        // 🔥 NEW (SEO)
        'phone',
        'email',
        'address',
        'city',
        'country',
        'lat',
        'lng',
        'open_time',
        'close_time',
        'facebook',
        'instagram',
        'linkedin',
    ];

    protected $casts = [
        'share_buttons' => 'array',
        'footer_brand_soc'=> 'array',
        'footer_headers'=> 'array',
        'footer_contact_area'=> 'array',
        'seo'=> 'array',

    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('favicon')->singleFile();
    }
    public function getFaviconUrlAttribute(): ?string
    {
        try {
            return $this->getFirstMediaUrl('favicon') ?: '/favicon.ico';
        } catch (\Throwable $e) {
            return '/favicon.ico';
        }
    }

    public function getFaviconVersionAttribute(): int
    {
        try {
            $media = $this->getFirstMedia('favicon');
            return $media?->updated_at?->timestamp ?? 1;
        } catch (\Throwable $e) {
            return 1;
        }
    }




    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('favicon_16')
            ->width(16)->height(16)->format('png');

        $this->addMediaConversion('favicon_32')
            ->width(32)->height(32)->format('png');

        $this->addMediaConversion('favicon_48')
            ->width(48)->height(48)->format('png');

        $this->addMediaConversion('favicon_64')
            ->width(64)->height(64)->format('png');

        $this->addMediaConversion('apple_touch')
            ->width(180)->height(180)->format('png');

        $this->addMediaConversion('android_192')
            ->width(192)->height(192)->format('png');

        $this->addMediaConversion('android_512')
            ->width(512)->height(512)->format('png');
    }


    public function getFaviconUrlsAttribute(): array
    {
        $media = $this->getFirstMedia('favicon');

        if (!$media) {
            return [];
        }

        return [
            '16' => $media->getUrl('favicon_16'),
            '32' => $media->getUrl('favicon_32'),
            '48' => $media->getUrl('favicon_48'),
            '64' => $media->getUrl('favicon_64'),
            'apple' => $media->getUrl('apple_touch'),
            'android_192' => $media->getUrl('android_192'),
            'android_512' => $media->getUrl('android_512'),
        ];
    }
}
