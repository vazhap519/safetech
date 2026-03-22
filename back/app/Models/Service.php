<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'service_section_title',
'service_section_description',
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
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }
}