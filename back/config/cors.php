<?php

$defaultFrontendUrl = env('APP_ENV', 'production') === 'production'
    ? 'https://safetech.ge'
    : 'http://localhost:3000';
$frontendUrls = env('FRONTEND_URLS', env('FRONTEND_URL', $defaultFrontendUrl));
$allowedOrigins = [
    'https://safetech.ge',
    'https://www.safetech.ge',
    ...array_map('trim', explode(',', (string) $frontendUrls)),
];

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => array_values(array_unique(array_filter($allowedOrigins))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Accept', 'Content-Type', 'Origin', 'X-Requested-With'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
