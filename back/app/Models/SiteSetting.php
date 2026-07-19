<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['value' => 'array', 'is_public' => 'boolean'];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function registerMediaCollections(): void
    {
        foreach (['logo', 'footer_logo', 'favicon', 'default_image'] as $collection) {
            $this->addMediaCollection($collection)->useDisk('public')->singleFile();
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Max, 1600, 1600)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('logo', 'footer_logo', 'favicon', 'default_image')
            ->nonQueued();
    }

    public function brandingMediaUrl(string $collection): ?string
    {
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('webp')
            ? $media->getUrl('webp')
            : $media->getUrl();
    }
}
