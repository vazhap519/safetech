<?php

return [
    'disk_min_free_percent' => (float) env('HEALTH_DISK_MIN_FREE_PERCENT', 10),
    'memory_min_available_percent' => (float) env('HEALTH_MEMORY_MIN_AVAILABLE_PERCENT', 5),
];
