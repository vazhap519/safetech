<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;

final class AdminCacheManager
{
    public static function clear(): void
    {
        Artisan::call('optimize:clear');

        // Re-establish the public-content version after Laravel's application
        // cache has been cleared and invalidate the Next.js CMS cache as well.
        PublicContentCache::flush();
        FrontendRevalidator::revalidate('cms');
    }
}
