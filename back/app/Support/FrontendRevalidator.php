<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $response = Http::timeout(3)
                ->withHeaders(['x-secret' => $secret])
                ->post("{$frontendUrl}/api/revalidate", array_filter([
                    'tag' => $tag,
                    'path' => $path,
                ], fn ($value) => $value !== null && $value !== ''));

            if (! $response->successful()) {
                Log::warning('Frontend cache revalidation request failed.', [
                    'status' => $response->status(),
                    'tag' => $tag,
                    'path' => $path,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Frontend cache revalidation could not be delivered.', [
                'tag' => $tag,
                'path' => $path,
                'exception' => $exception::class,
            ]);

            // Revalidation should never break the CMS/API write request.
        }
    }
}
