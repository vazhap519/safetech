<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Estimate extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'camera_megapixels' => 'decimal:2',
            'camera_unit_cost' => 'decimal:2',
            'recorder_unit_cost' => 'decimal:2',
            'cable_meters' => 'decimal:2',
            'cable_unit_cost' => 'decimal:2',
            'trunking_meters' => 'decimal:2',
            'trunking_unit_cost' => 'decimal:2',
            'installation_cost' => 'decimal:2',
            'hdd_cost_per_tb' => 'decimal:2',
            'poe_switch_unit_cost' => 'decimal:2',
            'connector_unit_cost' => 'decimal:2',
            'markup_rate' => 'decimal:4',
            'required_storage_tb' => 'decimal:2',
            'cost_total' => 'decimal:2',
            'markup_total' => 'decimal:2',
            'final_total' => 'decimal:2',
            'profit_total' => 'decimal:2',
            'manual_items' => 'array',
            'calculation' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::creating(function (Estimate $estimate): void {
            if (filled($estimate->estimate_number)) {
                return;
            }

            $estimate->estimate_number = sprintf(
                'EST-%s-%s',
                now()->format('YmdHis'),
                Str::upper(Str::random(4)),
            );
        });
    }
}
