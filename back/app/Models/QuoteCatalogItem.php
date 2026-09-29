<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteCatalogItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'markup_percentage' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'warranty_months' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function effectiveSalePrice(float $fallback = 0): float
    {
        if (is_numeric($this->sale_price) && (float) $this->sale_price > 0) {
            return round((float) $this->sale_price, 2);
        }

        if (is_numeric($this->purchase_price) && (float) $this->purchase_price > 0) {
            return round(
                (float) $this->purchase_price * (1 + max(0, (float) $this->markup_percentage) / 100),
                2,
            );
        }

        return round(max(0, $fallback), 2);
    }
}
