<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// 🔥 Spatie imports
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Hero extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'badge',
        'title',
        'subtitle',
        'primary_text',
        'primary_link',
        'secondary_text',
        'secondary_link'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->width(1200)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
    }
}