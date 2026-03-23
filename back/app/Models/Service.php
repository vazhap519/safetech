<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use InteractsWithMedia ,HasFactory;

    protected $fillable = [
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
        'seo_text' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('services')
            ->useDisk('public'); // ✅ აქ
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }
}
