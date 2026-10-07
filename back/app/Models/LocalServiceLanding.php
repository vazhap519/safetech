<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\CmsMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LocalServiceLanding extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'faq' => 'array',
            'keywords' => 'array',
            'schema' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
            'noindex' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $landing): void {
            $landing->location_slug = Str::slug(
                (string) ($landing->location_slug ?: $landing->location_name),
            );
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
            ->whereNotNull('location_slug')
            ->whereRaw("TRIM(COALESCE(location_slug, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(location_name, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(title, '')) <> ''")
            ->whereRaw("TRIM(COALESCE(content, '')) <> ''")
            ->whereHas('service', fn (Builder $service): Builder => $service->publiclyVisible())
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')
            ->useDisk('public')
            ->acceptsMimeTypes(CmsMedia::IMAGE_MIME_TYPES)
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('og_image')
            ->nonQueued();
    }

    public function getOgImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('og_image');

        return $media && $media->hasGeneratedConversion('og') && is_file($media->getPath('og'))
            ? $media->getUrl('og')
            : $media?->getUrl();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'local_service_landing_project',
            'landing_id',
            'project_id',
        )->withTimestamps();
    }

    public function publicProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'local_service_landing_project',
            'landing_id',
            'project_id',
        )
            ->publiclyVisible()
            ->withTimestamps();
    }
}
