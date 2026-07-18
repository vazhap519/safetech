<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

final class FrontendRevalidator
{
    public static function revalidate(?string $tag = null, ?string $path = null): void
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', ''), '/');
        $secret = (string) config('app.revalidate_secret', '');

        if ($frontendUrl === '' || $secret === '' || ($tag === null && $path === null)) {
            return;
        }

        try {
            Http::timeout(3)
                ->withHeaders(['x-secret' => $secret])
                ->post("{$frontendUrl}/api/revalidate", array_filter([
                    'tag' => $tag,
                    'path' => $path,
                ], fn ($value) => $value !== null && $value !== ''));
        } catch (\Throwable) {
            // Revalidation should never break the CMS/API write request.
        }
    }
}
