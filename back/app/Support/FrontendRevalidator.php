<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class FrontendRevalidator
{
    /** @var array<string, array{tag: ?string, path: ?string}> */
    private static array $pending = [];

    private static bool $terminatingCallbackRegistered = false;

    public static function revalidate(?string $tag = null, ?string $path = null): void
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', ''), '/');
        $secret = (string) config('app.revalidate_secret', '');

        if ($frontendUrl === '' || $secret === '' || ($tag === null && $path === null)) {
            return;
        }

        $key = ($tag ?? '').'|'.($path ?? '');
        self::$pending[$key] = ['tag' => $tag, 'path' => $path];

        if (app()->runningInConsole()) {
            self::flushPending($frontendUrl, $secret);

            return;
        }

        if (self::$terminatingCallbackRegistered) {
            return;
        }

        self::$terminatingCallbackRegistered = true;

        app()->terminating(function () use ($frontendUrl, $secret): void {
            self::flushPending($frontendUrl, $secret);
            self::$terminatingCallbackRegistered = false;
        });
    }

    private static function flushPending(string $frontendUrl, string $secret): void
    {
        $pending = self::$pending;
        self::$pending = [];

        foreach ($pending as $request) {
            self::deliver(
                $frontendUrl,
                $secret,
                $request['tag'],
                $request['path'],
            );
        }
    }

    private static function deliver(
        string $frontendUrl,
        string $secret,
        ?string $tag,
        ?string $path,
    ): void {
        try {
            $response = Http::connectTimeout(1)
                ->timeout(3)
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

            // Revalidation should never break or delay the CMS/API write response.
        }
    }
}
