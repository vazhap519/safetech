<?php

namespace App\Models\Concerns;

use App\Support\FrontendRevalidator;
use App\Support\PublicContentCache;

trait FlushesPublicContentCache
{
    protected static function bootFlushesPublicContentCache(): void
    {
        static::saved(fn () => self::refreshPublicContent());
        static::deleted(fn () => self::refreshPublicContent());
    }

    private static function refreshPublicContent(): void
    {
        PublicContentCache::flush();

        if (! app()->runningInConsole()) {
            FrontendRevalidator::revalidate('cms');
        }
    }
}
