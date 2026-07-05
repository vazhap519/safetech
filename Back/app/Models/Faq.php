<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use FlushesPublicContentCache;
    protected $guarded = ['id'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true)->orderBy('sort_order'); }
}
