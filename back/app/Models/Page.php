<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\CmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Page extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
            'noindex' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $page): void {
            if (filled($page->slug)) {
                return;
            }

            $page->slug = Str::slug($page->title) ?: 'page';
        });
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $published): void {
                $published
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereNotNull('slug')
            ->whereRaw("TRIM(COALESCE(slug, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(title, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(content, '')) <> ''")
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useDisk('public')
            ->acceptsMimeTypes(CmsMedia::IMAGE_MIME_TYPES)
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1600, 900)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('cover')
            ->nonQueued();
    }

    public function getCoverImageAttribute(): ?string
    {
        $media = $this->getFirstMedia('cover');

        return $media?->hasGeneratedConversion('webp')
            ? $media->getUrl('webp')
            : $media?->getUrl();
    }
}
