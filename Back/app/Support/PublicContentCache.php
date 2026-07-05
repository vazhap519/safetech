<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class PublicContentCache
{
    private const VERSION_KEY = 'public-content:version';

    public static function key(string $key): string
    {
        return 'public-content:'.Cache::get(self::VERSION_KEY, 1).':'.$key;
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, ((int) Cache::get(self::VERSION_KEY, 1)) + 1);
    }
}
