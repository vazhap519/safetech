<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Models\Concerns\HasActiveOrder;
use App\Models\Concerns\HasOptimizedSingleImage;
use App\Support\CmsMedia;
use App\Support\TeamMemberSocialLinks;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TeamMember extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use HasActiveOrder;
    use HasOptimizedSingleImage;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['socials' => 'array', 'translations' => 'array', 'is_active' => 'boolean'];
    }

    public function setSocialsAttribute(mixed $value): void
    {
        $this->attributes['socials'] = json_encode(
            TeamMemberSocialLinks::normalize($value),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $this->optimizedImageUrl($value);
    }

    public function registerMediaCollections(): void
    {
        $this->registerOptimizedImageCollection();

        $this->addMediaCollection('certificates')
            ->useDisk('public')
            ->acceptsMimeTypes(CmsMedia::IMAGE_MIME_TYPES);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerOptimizedImageConversions($media);

        $this->addMediaConversion('certificate-webp')
            ->fit(Fit::Max, 1800, 1800)
            ->format('webp')
            ->quality(86)
            ->performOnCollections('certificates')
            ->nonQueued();

        $this->addMediaConversion('certificate-thumb')
            ->fit(Fit::Max, 480, 480)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('certificates')
            ->nonQueued();
    }

    public function getCertificateImagesAttribute(): array
    {
        $fullName = trim("{$this->first_name} {$this->last_name}");

        return $this->getMedia('certificates')
            ->values()
            ->map(function (Media $media, int $index) use ($fullName): array {
                $source = $media->hasGeneratedConversion('certificate-webp')
                    ? $media->getUrl('certificate-webp')
                    : $media->getUrl();
                $thumbnail = $media->hasGeneratedConversion('certificate-thumb')
                    ? $media->getUrl('certificate-thumb')
                    : $source;

                return [
                    'id' => $media->id,
                    'src' => $source,
                    'thumbnail' => $thumbnail,
                    'alt' => $media->getCustomProperty('alt')
                        ?: "{$fullName} — certificate ".($index + 1),
                ];
            })
            ->all();
    }

    protected function imageCollectionName(): string
    {
        return 'image';
    }
}
