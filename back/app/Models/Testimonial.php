<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Models\Concerns\HasActiveOrder;
use App\Models\Concerns\HasOptimizedSingleImage;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Testimonial extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use HasActiveOrder;
    use HasOptimizedSingleImage;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['translations' => 'array', 'is_active' => 'boolean'];
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $this->optimizedImageUrl($value);
    }

    public function registerMediaCollections(): void
    {
        $this->registerOptimizedImageCollection();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerOptimizedImageConversions($media);
    }

    protected function imageCollectionName(): string
    {
        return 'image';
    }
}
