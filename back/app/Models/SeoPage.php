<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\MultilingualContent;
use App\Support\SiteSettings;
use App\Support\SocialLinks;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SeoPage extends Model implements HasMedia
{
    use FlushesPublicContentCache, InteractsWithMedia;

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

    public function seoable()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->slug) {
                $model->slug = '/'.ltrim($model->slug, '/');
                $model->canonical = SocialLinks::frontendUrl($model->slug);
            }

            if (is_array($model->keywords)) {
                $model->keywords = collect($model->keywords)
                    ->map(fn ($k) => is_array($k) ? $k : ['value' => $k])
                    ->toArray();
            }
        });
    }

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

    public function getOgImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('og_image', 'og')
            ?: SiteSettings::brandingMediaUrl('default_image')
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
            'schema' => $this->schemaDataForLocale($locale),
            'schemaOverride' => $this->schema ?: null,
        ];
    }

    public static function getByKey(string $key): ?self
    {
        return self::query()->where('key', $key)->first();
    }

    public static function resolve(?string $key = null, $model = null): array
    {
        $seo = null;

        if ($model) {
            $seo = self::query()
                ->where('seoable_type', get_class($model))
                ->where('seoable_id', $model->getKey())
                ->first();
        }

        if (! $seo && $key) {
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
        return $this->schemaDataForLocale('ka');
    }

    private function schemaDataForLocale(string $locale): array
    {
        if ($this->schema) {
            $schema = is_array($this->schema)
                ? $this->schema
                : json_decode($this->schema, true);

            return is_array($schema) ? $schema : [];
        }

        $settings = SiteSettings::businessProfile();
        $baseUrl = SocialLinks::frontendUrl('/');
        $entityBaseUrl = rtrim($baseUrl, '/').'/';
        $pageUrl = $this->localizedCanonical($locale);
        $sameAs = SocialLinks::sameAs($settings);
        $logo = SiteSettings::brandingMediaUrl('logo');
        $siteName = $settings->site_name ?: config('app.name');
        $siteDescription = $settings->site_description ?: null;
        $organizationId = "{$entityBaseUrl}#organization";
        $websiteId = "{$entityBaseUrl}#website";
        $organizationRef = ['@id' => $organizationId];
        $websiteRef = ['@id' => $websiteId];
        $hasAddress = filled($settings->address)
            || filled($settings->city)
            || filled($settings->postal_code);
        $hasGeo = filled($settings->lat) && filled($settings->lng);
        $hasOpeningHours = filled($settings->open_time) && filled($settings->close_time);
        $postalAddress = $hasAddress
            ? array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->address,
                'addressLocality' => $settings->city,
                'postalCode' => $settings->postal_code,
                'addressCountry' => $settings->country ?: 'GE',
            ], fn ($value): bool => filled($value))
            : null;
        $geo = $hasGeo
            ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $settings->lat,
                'longitude' => $settings->lng,
            ]
            : null;
        $openingHours = $hasOpeningHours
            ? [[
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => $settings->open_time,
                'closes' => $settings->close_time,
            ]]
            : null;

        switch ($this->schema_type) {
            case 'WebSite':
                return [
                    array_filter([
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        '@id' => $organizationId,
                        'name' => $siteName,
                        'url' => $entityBaseUrl,
                        'logo' => $logo,
                        'description' => $siteDescription,
                        'telephone' => $settings->phone,
                        'email' => $settings->email,
                        'sameAs' => $sameAs ?: null,
                        'areaServed' => $settings->country ?: 'GE',
                    ], fn ($value): bool => $value !== null && $value !== '' && $value !== []),
                    array_filter([
                        '@context' => 'https://schema.org',
                        '@type' => 'WebSite',
                        '@id' => $websiteId,
                        'name' => $siteName,
                        'url' => $entityBaseUrl,
                        'description' => $siteDescription,
                        'publisher' => $organizationRef,
                    ], fn ($value): bool => $value !== null && $value !== '' && $value !== []),
                ];

            case 'Article':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $this->title,
                    'description' => $this->description,
                    'image' => $this->og_image_url,
                    'datePublished' => $this->created_at,
                    'mainEntityOfPage' => $pageUrl,
                    'inLanguage' => $locale,
                    'author' => $organizationRef,
                    'publisher' => $organizationRef,
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

            case 'LocalBusiness':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'LocalBusiness',
                    '@id' => "{$entityBaseUrl}#localbusiness",
                    'name' => $siteName,
                    'description' => $siteDescription,
                    'url' => $entityBaseUrl,
                    'logo' => $logo,
                    'image' => $logo,
                    'telephone' => $settings->phone,
                    'email' => $settings->email,
                    'address' => $postalAddress,
                    'geo' => $geo,
                    'openingHoursSpecification' => $openingHours,
                    'sameAs' => $sameAs ?: null,
                    'areaServed' => $settings->country ?: 'GE',
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

            case 'Service':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Service',
                    'name' => $this->title,
                    'description' => $this->description,
                    'url' => $pageUrl,
                    'inLanguage' => $locale,
                    'provider' => $organizationRef,
                    'areaServed' => $settings->country ?: 'GE',
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

            case 'AboutPage':
            case 'CollectionPage':
            case 'ContactPage':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => $this->schema_type,
                    'name' => $this->title,
                    'description' => $this->description,
                    'url' => $pageUrl,
                    'inLanguage' => $locale,
                    'isPartOf' => $websiteRef,
                    'about' => $organizationRef,
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

            case 'WebApplication':
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'WebApplication',
                    'name' => $this->title,
                    'description' => $this->description,
                    'url' => $pageUrl,
                    'inLanguage' => $locale,
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem' => 'Web',
                    'provider' => $organizationRef,
                    'isPartOf' => $websiteRef,
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);

            default:
                return array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $this->title,
                    'description' => $this->description,
                    'url' => $pageUrl,
                    'inLanguage' => $locale,
                    'isPartOf' => $websiteRef,
                ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);
        }
    }

    private function localizedCanonical(string $locale): string
    {
        $slug = $this->slug ?: '/';

        if ($locale === 'ka') {
            return $this->canonical ?: SocialLinks::frontendUrl($slug);
        }

        $localizedPath = $slug === '/'
            ? "/{$locale}"
            : "/{$locale}/".ltrim($slug, '/');

        return SocialLinks::frontendUrl($localizedPath);
    }
}
