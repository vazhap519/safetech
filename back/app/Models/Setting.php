<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
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
}
