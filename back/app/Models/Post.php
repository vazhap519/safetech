<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use FlushesPublicContentCache, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category_id',
        'author_id',
        'reading_time',
        'published_year',
        'meta_title',
        'meta_description',
        'seo_keywords',
        'noindex',
        'faq',
        'schema',
        'seo_author',
        'seo_published_at',
        'translations',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'is_published' => 'boolean',
            'seo_keywords' => 'array',
            'noindex' => 'boolean',
            'faq' => 'array',
            'schema' => 'array',
            'seo_published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            if ($post->slug) {
                return;
            }

            $baseSlug = Str::slug($post->title) ?: Str::lower(Str::random(10));
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $post->slug = $candidate;
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PostSection::class)->orderBy('position');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('slug')
            ->whereRaw("TRIM(COALESCE(slug, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(title, '')) <> ''")
            ->where(function (Builder $content): void {
                $content
                    ->whereRaw("TRIM(COALESCE(excerpt, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(body, '')) <> ''")
                    ->orWhereHas('sections', fn (Builder $section): Builder => $section
                        ->whereNotNull('content')
                        ->whereRaw("TRIM(COALESCE(content, '')) <> ''"));
            });
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
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('cover')
            ->nonQueued();
    }

    public function getImageAttribute(): ?string
    {
        try {
            $media = $this->getFirstMedia('cover');

            if (! $media) {
                return null;
            }

            return $media->hasGeneratedConversion('webp')
                ? $media->getUrl('webp')
                : $media->getUrl();
        } catch (\Throwable) {
            return null;
        }
    }
}
