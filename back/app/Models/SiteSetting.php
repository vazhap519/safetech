<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\CmsMedia;
use App\Support\SiteSettingValueNormalizer;
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

    protected static function booted(): void
    {
        static::saving(function (self $setting): void {
            if (is_string($setting->key) && $setting->key !== '') {
                $setting->value = SiteSettingValueNormalizer::normalize(
                    $setting->key,
                    $setting->value,
                );
            }
        });
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function registerMediaCollections(): void
    {
        foreach ([
            'logo',
            'footer_logo',
            'favicon',
            'default_image',
            'home_hero',
            'home_infrastructure',
            'services_hero',
            'projects_hero',
            'about_story',
            'contact_intro',
            'contact_support',
        ] as $collection) {
            $this->addMediaCollection($collection)
                ->useDisk('public')
                ->acceptsMimeTypes(CmsMedia::IMAGE_MIME_TYPES)
                ->singleFile();
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Max, 1600, 1600)
            ->format('webp')
            ->quality(82)
            ->performOnCollections(
                'logo',
                'footer_logo',
                'favicon',
                'default_image',
                'home_hero',
                'home_infrastructure',
                'services_hero',
                'projects_hero',
                'about_story',
                'contact_intro',
                'contact_support',
            )
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
