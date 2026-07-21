<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAudit extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChangedFieldsAttribute(): string
    {
        $fields = array_unique(array_merge(
            array_keys($this->old_values ?? []),
            array_keys($this->new_values ?? []),
        ));

        return implode(', ', $fields);
    }
}
