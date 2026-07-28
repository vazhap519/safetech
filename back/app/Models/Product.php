<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\PublicMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'filter_values' => 'array',
            'seo' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (filled($product->slug)) {
                return;
            }

            $baseSlug = Str::slug($product->name ?: 'product') ?: 'product';
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $product->slug = $candidate;
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
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
            ->whereRaw("TRIM(COALESCE(name, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(short_description, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(description, '')) <> ''")
            ->orderBy('sort_order')
            ->orderByDesc('published_at');
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
            ->fit(Fit::Crop, 1400, 900)
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

    public function getImageAttribute(): ?string
    {
        return $this->mediaUrl('cover', 'webp')
            ?? $this->mediaUrl('cover')
            ?? PublicMediaUrl::resolve($this->getRawOriginal('image'));
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->mediaUrl('cover', 'thumb')
            ?? $this->image;
    }

    public function getCardImageAttribute(): ?string
    {
        return $this->thumb_url ?: $this->image;
    }

    /** @return array<int, array{src: string, thumb: string, alt: string}> */
    public function getGalleryUrlsAttribute(): array
    {
        return $this->getMedia('gallery')
            ->map(fn (Media $media): array => [
                'src' => $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : (
                    $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : $media->getUrl()
                ),
                'alt' => $media->getCustomProperty('alt') ?: $this->image_alt ?: $this->name ?: '',
            ])
            ->values()
            ->all();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function normalizedFilterValues(): Collection
    {
        return collect($this->filter_values ?? [])
            ->map(function (mixed $value): ?array {
                if (! is_array($value)) {
                    return null;
                }

                $filterSlug = trim((string) ($value['filter_slug'] ?? ''));
                $optionSlugs = collect($value['option_slugs'] ?? [])
                    ->filter(fn (mixed $slug): bool => is_string($slug) && trim($slug) !== '')
                    ->map(fn (string $slug): string => trim($slug))
                    ->unique()
                    ->values()
                    ->all();

                if ($filterSlug === '' || $optionSlugs === []) {
                    return null;
                }

                return [
                    'filter_slug' => $filterSlug,
                    'option_slugs' => $optionSlugs,
                ];
            })
            ->filter()
            ->values();
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
