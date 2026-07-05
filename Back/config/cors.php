<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', env('FRONTEND_URLS', 'http://localhost:3000'))))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Accept', 'Content-Type', 'Origin'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
