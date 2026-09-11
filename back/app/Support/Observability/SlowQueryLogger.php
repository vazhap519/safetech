<?php

namespace App\Support\Observability;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class SlowQueryLogger
{
    public function __construct(
        private float $thresholdMs,
        private string $channel,
    ) {}

    public function __invoke(QueryExecuted $query): void
    {
        if ($query->time < $this->thresholdMs) {
            return;
        }

        // Bindings and interpolated SQL are intentionally excluded because
        // lead and account values must never be copied into operational logs.
        try {
            Log::channel($this->channel)->warning('Slow database query detected.', [
                'connection' => $query->connectionName,
                'duration_ms' => round($query->time, 2),
                'sql_template' => $query->sql,
            ]);
        } catch (Throwable) {
            // Observability is deliberately fail-safe: a log disk or
            // permission problem must never break a public or admin request.
        }
    }

    public function listener(): Closure
    {
        return function (QueryExecuted $query): void {
            $this($query);
        };
    }
}
