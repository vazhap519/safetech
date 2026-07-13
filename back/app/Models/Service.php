<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use FlushesPublicContentCache;

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
            'seo' => 'array',
            'lead_form' => 'array',
            'translations' => 'array',
            'is_published' => 'boolean',
        ];
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
}
