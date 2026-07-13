<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use FlushesPublicContentCache;
    protected $guarded = ['id'];
    protected function casts(): array { return ['value' => 'array', 'is_public' => 'boolean']; }
    public function scopePublic(Builder $query): Builder { return $query->where('is_public', true); }
}
