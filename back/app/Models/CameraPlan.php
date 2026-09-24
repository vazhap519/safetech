<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CameraPlan extends Model
{
    protected $fillable = [
        'title', 'contact_name', 'contact_phone', 'contact_email',
        'status', 'layout', 'background_path', 'privacy_accepted_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'layout' => 'array',
            'privacy_accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (self $plan): void {
            if ($plan->background_path) {
                Storage::disk('local')->delete($plan->background_path);
            }
        });
    }
}
