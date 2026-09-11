<?php

return [
    'slow_queries' => [
        'enabled' => filter_var(
            env('SLOW_QUERY_LOG_ENABLED', env('APP_ENV') === 'production'),
            FILTER_VALIDATE_BOOL,
        ),
        'threshold_ms' => max(1, (int) env('SLOW_QUERY_THRESHOLD_MS', 100)),
        'channel' => env('SLOW_QUERY_LOG_CHANNEL', 'slow_queries'),
    ],
];
