<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\PublicMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'meta' => 'array', 'scope' => 'array', 'specs' => 'array', 'challenges' => 'array',
            'solutions' => 'array', 'process' => 'array', 'gallery' => 'array', 'results' => 'array',
            'testimonial' => 'array', 'related' => 'array', 'is_featured' => 'boolean',
            'seo' => 'array', 'translations' => 'array', 'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('slug')
            ->whereRaw("TRIM(COALESCE(slug, '')) <> ''")
            ->where(function (Builder $headline): void {
                $headline
                    ->whereRaw("TRIM(COALESCE(name, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(title, '')) <> ''");
            })
            ->where(function (Builder $content): void {
                $content
                    ->whereRaw("TRIM(COALESCE(description, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(excerpt, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(content, '')) <> ''");
            })
            ->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('gallery')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1600, 1000)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('cover', 'gallery')
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 720, 480)
            ->format('webp')
            ->quality(78)
            ->performOnCollections('cover', 'gallery')
            ->nonQueued();
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->mediaUrl('cover', 'webp')
            ?? $this->mediaUrl('cover')
            ?? PublicMediaUrl::resolve($this->getRawOriginal('image'));
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->mediaUrl('cover', 'thumb')
            ?? $this->cover_url;
    }

    public function getGalleryUrlsAttribute(): array
    {
        $mediaItems = $this->getMedia('gallery')
            ->map(fn (Media $media): array => [
                'src' => $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : $media->getUrl(),
                'alt' => $media->getCustomProperty('alt') ?: $this->image_alt ?: $this->title ?: $this->name ?: '',
            ])
            ->values()
            ->all();

        if ($mediaItems) {
            return $mediaItems;
        }

        return collect($this->gallery ?? [])
            ->map(function ($item): ?array {
                if (is_string($item)) {
                    return [
                        'src' => PublicMediaUrl::resolve($item) ?? $item,
                        'alt' => $this->image_alt ?: $this->title ?: $this->name ?: '',
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                $src = $item['src'] ?? $item['image'] ?? null;

                if (! $src) {
                    return null;
                }

                return [
                    'src' => PublicMediaUrl::resolve($src) ?? $src,
                    'alt' => $item['alt'] ?? $this->image_alt ?? $this->title ?? $this->name ?? '',
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function getCategorySlugAttribute(): string
    {
        $category = $this->relationLoaded('category') ? $this->getRelation('category') : null;

        return $category?->slug
            ?: (string) ($this->getRawOriginal('category') ?: 'offices');
    }

    public function getCategoryNameAttribute(): string
    {
        $category = $this->relationLoaded('category') ? $this->getRelation('category') : null;

        return $category?->name
            ?: (string) ($this->getRawOriginal('category') ?: $this->category_slug);
    }

    private function mediaUrl(string $collection, string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        if ($conversion && $media->hasGeneratedConversion($conversion)) {
            return $media->getUrl($conversion);
        }

        return $media->getUrl();
    }
}
