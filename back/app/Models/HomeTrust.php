<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeTrust extends Model implements HasMedia
{
use InteractsWithMedia;

protected $fillable = ['is_active'];

protected $casts = [
'is_active' => 'boolean',
];
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logos')
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
            ->quality(80)
            ->nonQueued() // 🔥 დაამატე (ძალიან მნიშვნელოვანია)
            ->performOnCollections('logos');
    }

    /**
     * ✅ Clean accessor (Hero style)
     */
    public function getLogosAttribute()
    {
        return $this->getMedia('logos')
            ->map(function ($media) {
                return [
                    'image' => $media->getFullUrl('webp') // 🔥 აქაა მთავარი fix
                ];
            })
            ->values();
    }
}
