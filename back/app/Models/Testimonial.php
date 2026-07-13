<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use FlushesPublicContentCache;
    protected $guarded = ['id'];
    protected function casts(): array { return ['translations' => 'array', 'is_active' => 'boolean']; }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true)->orderBy('sort_order'); }
}
