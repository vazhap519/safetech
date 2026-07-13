<?php

namespace App\Models\Concerns;

use App\Support\PublicContentCache;

trait FlushesPublicContentCache
{
    protected static function bootFlushesPublicContentCache(): void
    {
        static::saved(fn () => PublicContentCache::flush());
        static::deleted(fn () => PublicContentCache::flush());
    }
}
