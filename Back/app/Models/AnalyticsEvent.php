<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    public const TYPE_SERVICE_VIEW = 'service_view';

    public const TYPE_WHATSAPP_CLICK = 'whatsapp_click';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('event_type', $type);
    }

    public function scopeServiceViews(Builder $query): Builder
    {
        return $query->forType(self::TYPE_SERVICE_VIEW);
    }

    public function scopeWhatsAppClicks(Builder $query): Builder
    {
        return $query->forType(self::TYPE_WHATSAPP_CLICK);
    }
}
