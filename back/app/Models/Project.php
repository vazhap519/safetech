<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'video_url',
        'seo',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'seo' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /* =========================
       RELATION
    ========================= */
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class)->withDefault();
    }

    /* =========================
       SCOPE
    ========================= */
    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', true);
    }

    /* =========================
       MEDIA COLLECTIONS
    ========================= */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile();

        $this->addMediaCollection('gallery');
    }

    /* =========================
       🔥 CRITICAL FIX
    ========================= */
    public function registerMediaConversions(?Media $media = null): void
    {
        /* thumb (LIST VIEW) */
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->format('webp')
            ->quality(80)
            ->nonQueued(); // 🔥 ეს იყო პრობლემა

        /* full webp */
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(80)
            ->nonQueued(); // 🔥 ესეც აუცილებელია
    }

    /* =========================
       ACCESSORS
    ========================= */

    public function getThumbUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('cover', 'thumb')
            ?: $this->getFirstMediaUrl('cover')
                ?: '/placeholder.jpg';
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('cover', 'webp')
            ?: $this->getFirstMediaUrl('cover')
                ?: '/placeholder.jpg';
    }

    public function getGalleryUrlsAttribute(): array
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return [
                'url' => $media->getUrl('webp') ?: $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
            ];
        })->toArray();
    }

}
