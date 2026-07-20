<?php

namespace App\Models\Concerns;

use App\Support\PublicMediaUrl;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasOptimizedSingleImage
{
    abstract protected function imageCollectionName(): string;

    protected function registerOptimizedImageCollection(): void
    {
        $this->addMediaCollection($this->imageCollectionName())
            ->useDisk('public')
            ->singleFile();
    }

    protected function registerOptimizedImageConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Max, 1200, 1200)
            ->format('webp')
            ->quality(82)
            ->performOnCollections($this->imageCollectionName())
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 320, 320)
            ->format('webp')
            ->quality(78)
            ->performOnCollections($this->imageCollectionName())
            ->nonQueued();
    }

    protected function optimizedImageUrl(?string $legacyPath): ?string
    {
        $media = $this->getFirstMedia($this->imageCollectionName());

        if ($media) {
            return $media->hasGeneratedConversion('webp')
                ? $media->getUrl('webp')
                : $media->getUrl();
        }

        return PublicMediaUrl::resolve($legacyPath);
    }
}
