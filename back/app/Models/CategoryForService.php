<?php

namespace App\Models;

use App\Filament\Support\StableSlug;
use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\SocialLinks;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CategoryForService extends Model implements HasMedia
{
    use FlushesPublicContentCache, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'noindex',
        'translations',
    ];

    protected $casts = [
        'seo_keywords' => 'array',
        'faq' => 'array',
        'schema' => 'array',
        'translations' => 'array',
        'noindex' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')
            ->useDisk('public')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('og_image')
            ->nonQueued();
    }

    public function getOgImageUrlAttribute(): ?string
    {
        $url = $this->getFirstMediaUrl('og_image', 'og');

        return $url !== '' ? $url : null;
    }

    protected static function booted()
    {
        static::saving(function (self $category): void {
            $translations = is_array($category->translations) ? $category->translations : [];

            data_set($translations, 'fields.name.ka', trim((string) $category->name));

            if (blank(data_get($translations, 'seo.canonical')) && filled($category->slug)) {
                data_set(
                    $translations,
                    'seo.canonical',
                    SocialLinks::frontendUrl('/services/category/'.ltrim((string) $category->slug, '/')),
                );
            }

            $category->translations = $translations;
        });

        static::creating(function ($category) {
            if ($category->slug === null || $category->slug === '') {
                $category->slug = StableSlug::fromTitle($category->name);
            }
        });
    }

    /*
    |------------------------------------------------------------------
    | 🔗 RELATION
    |------------------------------------------------------------------
    */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_for_service_id');
    }
}
