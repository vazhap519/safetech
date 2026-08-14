<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\CmsMedia;
use App\Support\PublicMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Service extends Model implements HasMedia
{
    use FlushesPublicContentCache;
    use InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'highlights' => 'array',
            'overview' => 'array',
            'benefits' => 'array',
            'solutions' => 'array',
            'industries' => 'array',
            'process' => 'array',
            'brands' => 'array',
            'features' => 'array',
            'faq' => 'array',
            'seo' => 'array',
            'lead_form' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Service $service): void {
            if (filled($service->slug)) {
                return;
            }

            $baseSlug = Str::slug($service->name ?: $service->title ?: 'service') ?: 'service';
            $candidate = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $candidate)->exists()) {
                $candidate = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $service->slug = $candidate;
        });

        static::saving(function (Service $service): void {
            $service->fillMissingCardTranslations();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryForService::class, 'category_for_service_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    public function localServiceLandings(): HasMany
    {
        return $this->hasMany(LocalServiceLanding::class);
    }

    public function publicLocalServiceLandings(): HasMany
    {
        return $this->hasMany(LocalServiceLanding::class)
            ->publiclyVisible();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('slug')
            ->whereRaw("TRIM(COALESCE(slug, '')) <> ''")
            ->where(function (Builder $headline): void {
                $headline
                    ->whereRaw("TRIM(COALESCE(name, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(title, '')) <> ''");
            })
            ->where(function (Builder $content): void {
                $content
                    ->whereRaw("TRIM(COALESCE(description, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(short_description, '')) <> ''")
                    ->orWhereRaw("TRIM(COALESCE(long_description, '')) <> ''");
            })
            ->orderBy('sort_order');
    }

    public function scopeWithAnalyticsSummary(Builder $query): Builder
    {
        return $query
            ->select('services.*')
            ->selectSub(
                AnalyticsEvent::query()
                    ->serviceViews()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('service_id', 'services.id'),
                'total_views_count',
            )
            ->selectSub(
                AnalyticsEvent::query()
                    ->serviceViews()
                    ->selectRaw('COUNT(DISTINCT visitor_hash)')
                    ->whereColumn('service_id', 'services.id'),
                'unique_viewers_count',
            )
            ->selectSub(
                AnalyticsEvent::query()
                    ->whatsAppClicks()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('service_id', 'services.id'),
                'whatsapp_clicks_count',
            )
            ->selectSub(
                AnalyticsEvent::query()
                    ->whatsAppClicks()
                    ->selectRaw('COUNT(DISTINCT visitor_hash)')
                    ->whereColumn('service_id', 'services.id'),
                'unique_whatsapp_clickers_count',
            );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('services')
            ->useDisk('public')
            ->acceptsMimeTypes(CmsMedia::IMAGE_MIME_TYPES)
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->fit(Fit::Crop, 1400, 900)
            ->format('webp')
            ->quality(82)
            ->performOnCollections('services')
            ->nonQueued();

        $this->addMediaConversion('social')
            ->fit(Fit::Crop, 1200, 630)
            ->format('webp')
            ->quality(85)
            ->performOnCollections('services')
            ->nonQueued();

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 720, 480)
            ->format('webp')
            ->quality(78)
            ->performOnCollections('services')
            ->nonQueued();
    }

    public function getImageAttribute(): ?string
    {
        $media = $this->getFirstMedia('services');

        if ($media) {
            return $media->hasGeneratedConversion('webp')
                ? $media->getUrl('webp')
                : $media->getUrl();
        }

        return PublicMediaUrl::resolve($this->getRawOriginal('hero_image'));
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->image;
    }

    public function getSocialImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('services');

        if ($media?->hasGeneratedConversion('social')) {
            return $media->getUrl('social');
        }

        return $this->image;
    }

    private function fillMissingCardTranslations(): void
    {
        $translations = is_array($this->translations) ? $this->translations : [];

        foreach (['ka', 'en', 'ru'] as $locale) {
            $localizedName = trim((string) data_get($translations, "fields.name.{$locale}", ''));
            $localizedTitle = trim((string) data_get($translations, "fields.title.{$locale}", ''));
            $localizedDescription = trim((string) data_get($translations, "fields.description.{$locale}", ''));

            $titleFallback = $localizedName !== '' ? $localizedName : $localizedTitle;
            $descriptionFallback = $localizedDescription;

            if ($locale === 'ka') {
                $titleFallback = $titleFallback !== ''
                    ? $titleFallback
                    : trim((string) ($this->name ?: $this->title));
                $descriptionFallback = $descriptionFallback !== ''
                    ? $descriptionFallback
                    : trim((string) ($this->description ?: $this->short_description));
            }

            if (
                blank(data_get($translations, "fields.card.title.{$locale}"))
                && $titleFallback !== ''
            ) {
                data_set($translations, "fields.card.title.{$locale}", $titleFallback);
            }

            if (
                blank(data_get($translations, "fields.card.description.{$locale}"))
                && $descriptionFallback !== ''
            ) {
                data_set($translations, "fields.card.description.{$locale}", $descriptionFallback);
            }
        }

        $this->translations = $translations;
    }
}
