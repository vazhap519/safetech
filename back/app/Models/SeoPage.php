<?php

namespace App\Models;

use App\Support\MultilingualContent;
use App\Support\SiteSettings;
use App\Support\SocialLinks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SeoPage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'key',
        'slug',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'canonical',
        'noindex',
        'schema_type',
        'schema',
        'translations',
    ];

    protected $casts = [
        'keywords' => 'array',
        'noindex' => 'boolean',
        'schema' => 'array',
        'translations' => 'array',
    ];

    protected $appends = [
        'og_image_url',
        'share_image_url',
        'meta',
    ];

    protected $attributes = [
        'keywords' => '[]',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function seoable()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO FIX + CACHE
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saving(function ($model) {

            if ($model->slug) {
                $model->slug = '/' . ltrim($model->slug, '/');
                $model->canonical = SocialLinks::frontendUrl($model->slug);
            }

            if (is_array($model->keywords)) {
                $model->keywords = collect($model->keywords)
                    ->map(fn ($k) => is_array($k) ? $k : ['value' => $k])
                    ->toArray();
            }
        });

        static::saved(fn ($model) => self::clearCache($model));
        static::deleted(fn ($model) => self::clearCache($model));
    }

    protected static function clearCache($model): void
    {
        if ($model->key) {
            Cache::forget("seo_{$model->key}");
        }

        if ($model->seoable_id && $model->seoable_type) {
            Cache::forget("seo_model_{$model->seoable_type}_{$model->seoable_id}");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIA
    |--------------------------------------------------------------------------
    */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->useDisk('public')->singleFile();
        $this->addMediaCollection('share_image')->useDisk('public')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('og_image', 'share_image')
            ->nonQueued();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getOgImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('og_image', 'og')
            ?: SocialLinks::frontendUrl('/services/1.jpg');
    }

    public function getShareImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('share_image', 'og')
            ?: $this->og_image_url;
    }

    public function getKeywordsAttribute($value)
    {
        $keywords = is_array($value)
            ? $value
            : (json_decode($value ?: '[]', true) ?: []);

        return collect($keywords)
            ->map(fn ($k) => is_array($k) ? $k : ['value' => $k])
            ->toArray();
    }

    public function getKeywordsListAttribute(): array
    {
        return collect($this->keywords)
            ->pluck('value')
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | META
    |--------------------------------------------------------------------------
    */
    public function getMetaAttribute(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords_list,

            'canonical' => $this->canonical,
            'noindex' => $this->noindex,
            'robots' => $this->noindex ? 'noindex, nofollow' : 'index, follow',

            'og' => [
                'title' => $this->og_title ?: $this->title,
                'description' => $this->og_description ?: $this->description,
                'image' => $this->og_image_url,
            ],

            'share_image' => $this->share_image_url,
            'schema' => $this->schema_data,
            'schemaOverride' => $this->schema ?: null,
        ];
    }

    /** @return array<string, mixed> */
    public function localizedMeta(string $locale): array
    {
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
        $title = MultilingualContent::valuesForField($this, 'title', $this->title)[$locale] ?: $this->title;
        $description = MultilingualContent::valuesForField($this, 'description', $this->description)[$locale] ?: $this->description;
        $ogTitle = MultilingualContent::valuesForField($this, 'og_title', $this->og_title)[$locale] ?: ($this->og_title ?: $title);
        $ogDescription = MultilingualContent::valuesForField($this, 'og_description', $this->og_description)[$locale] ?: ($this->og_description ?: $description);
        $localizedKeywords = data_get($this->translations, "keywords.{$locale}");

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => is_array($localizedKeywords)
                ? array_values(array_filter($localizedKeywords, 'is_string'))
                : $this->keywords_list,
            'canonical' => $this->canonical,
            'noindex' => $this->noindex,
            'robots' => $this->noindex ? 'noindex, nofollow' : 'index, follow',
            'og' => [
                'title' => $ogTitle,
                'description' => $ogDescription,
                'image' => $this->og_image_url,
            ],
            'share_image' => $this->share_image_url,
            'schema' => $this->schema_data,
            'schemaOverride' => $this->schema ?: null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CACHE HELPERS
    |--------------------------------------------------------------------------
    */
    public static function getByKey(string $key): ?self
    {
        return Cache::remember("seo_$key", 3600, fn () =>
        self::where('key', $key)->first()
        );
    }

    public static function resolve(string $key = null, $model = null): array
    {
        $seo = null;

        if ($model) {
            $seo = Cache::remember(
                "seo_model_" . get_class($model) . "_{$model->getKey()}",
                3600,
                fn () => self::where('seoable_type', get_class($model))
                    ->where('seoable_id', $model->getKey())
                    ->first()
            );
        }

        if (!$seo && $key) {
            $seo = self::getByKey($key);
        }

        return $seo?->meta ?? [
            'title' => config('app.name'),
            'description' => '',
            'keywords' => [],
        ];
    }

    public function getSchemaDataAttribute(): array
    {
        if ($this->schema) {
            $schema = is_array($this->schema)
                ? $this->schema
                : json_decode($this->schema, true);

            return is_array($schema) ? $schema : [];
        }

        $settings = SiteSettings::businessProfile();
        $baseUrl = SocialLinks::frontendUrl('/');
        $sameAs = SocialLinks::sameAs($settings);

        switch ($this->schema_type) {

            case 'WebSite':
                return [
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        'name' => config('app.name'),
                        'url' => $baseUrl,
                        'sameAs' => $sameAs,
                    ],
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'WebSite',
                        'name' => config('app.name'),
                        'url' => $baseUrl,
                    ],
                ];

            case 'Article':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $this->title,
                    'description' => $this->description,
                    'image' => $this->og_image_url,
                    'datePublished' => $this->created_at,
                    'mainEntityOfPage' => $this->canonical ?: $baseUrl,
                    'author' => [
                        '@type' => 'Organization',
                        'name' => config('app.name'),
                    ],
                ]);

            case 'LocalBusiness':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'LocalBusiness',
                    'name' => config('app.name'),
                    'url' => $baseUrl,
                    'telephone' => $settings?->phone,
                    'email' => $settings?->email,
                    'address' => array_filter([
                        '@type' => 'PostalAddress',
                        'streetAddress' => $settings?->address,
                        'addressLocality' => $settings?->city,
                        'addressCountry' => $settings?->country ?: 'GE',
                    ]),
                    'geo' => array_filter([
                        '@type' => 'GeoCoordinates',
                        'latitude' => $settings?->lat,
                        'longitude' => $settings?->lng,
                    ]),
                    'openingHoursSpecification' => [
                        array_filter([
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                            'opens' => $settings?->open_time,
                            'closes' => $settings?->close_time,
                        ]),
                    ],
                    'sameAs' => $sameAs,
                    'areaServed' => $settings?->country ?: 'Georgia',
                ]);

            case 'Service':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'Service',
                    'name' => $this->title,
                    'description' => $this->description,
                    'provider' => [
                        '@type' => 'Organization',
                        'name' => config('app.name'),
                        'url' => $baseUrl,
                    ],
                ];

            default:
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $this->title,
                    'description' => $this->description,
                    'url' => $this->canonical,
                ];
        }
    }
}
