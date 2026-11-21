<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteCatalogPriceHistory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2', 'markup_percentage' => 'decimal:2',
            'sale_price' => 'decimal:2', 'effective_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(QuoteCatalogItem::class, 'quote_catalog_item_id');
    }
}
