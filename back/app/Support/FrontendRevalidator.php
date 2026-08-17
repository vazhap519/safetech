<?php

namespace App\Support;

use App\Jobs\RevalidateFrontend;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            try {
                RevalidateFrontend::dispatch(
                    $frontendUrl,
                    $secret,
                    $request['tag'],
                    $request['path'],
                );
            } catch (Throwable $exception) {
                Log::warning('Frontend cache revalidation could not be queued.', [
                    'tag' => $request['tag'],
                    'path' => $request['path'],
                    'exception' => $exception::class,
                ]);

                // CMS writes must never fail because cache revalidation could not be queued.
            }
        }
    }
}
