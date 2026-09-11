<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Throwable;

final class QueueWorkerHeartbeat
{
    private static int $lastRecordedAt = 0;

    public static function record(): void
    {
        $now = now()->timestamp;
        $interval = max(5, (int) config('queue.health_heartbeat_interval_seconds', 30));

        if (self::$lastRecordedAt > 0 && ($now - self::$lastRecordedAt) < $interval) {
            return;
        }

        try {
            Cache::put(self::key(), $now, now()->addSeconds($interval * 4));
            self::$lastRecordedAt = $now;
        } catch (Throwable) {
            // Observability must never interrupt the worker's actual jobs.
        }
    }

    public static function ageSeconds(): ?int
    {
        try {
            $timestamp = Cache::get(self::key());
        } catch (Throwable) {
            return null;
        }

        return is_numeric($timestamp)
            ? max(0, now()->timestamp - (int) $timestamp)
            : null;
    }

    public static function resetThrottle(): void
    {
        self::$lastRecordedAt = 0;
    }

    private static function key(): string
    {
        return 'health:queue-worker-heartbeat:'.config('queue.default', 'unknown');
    }
}
