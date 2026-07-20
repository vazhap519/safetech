<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicContentCache;
use App\Models\Concerns\HasActiveOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use FlushesPublicContentCache, HasActiveOrder;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['translations' => 'array', 'is_active' => 'boolean'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
