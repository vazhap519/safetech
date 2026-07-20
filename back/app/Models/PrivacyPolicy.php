<?php

namespace App\Models;

use App\Support\FrontendRevalidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PrivacyPolicy extends Model
{
    protected $fillable = [
        'title',
        'highlight',
        'content',
        'translations',
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    protected static function booted(): void
    {
        $refresh = static function (): void {
            foreach (['ka', 'en', 'ru'] as $locale) {
                Cache::forget("privacy_page:{$locale}");
            }

            if (! app()->runningInConsole()) {
                FrontendRevalidator::revalidate('privacy');
            }
        };

        static::saved($refresh);
        static::deleted($refresh);
    }
}
