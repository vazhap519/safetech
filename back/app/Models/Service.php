<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Support\PublicMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
}
