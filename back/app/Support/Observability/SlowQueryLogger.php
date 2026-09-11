<?php

namespace App\Support\Observability;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

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
        Log::channel($this->channel)->warning('Slow database query detected.', [
            'connection' => $query->connectionName,
            'duration_ms' => round($query->time, 2),
            'sql_template' => $query->sql,
        ]);
    }
}
