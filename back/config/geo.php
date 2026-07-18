<?php

return [
    'enabled' => (bool) env('GEO_BLOCK_ENABLED', false),
    'allowed_countries' => array_values(array_filter(array_map(
        fn (string $country): string => strtoupper(trim($country)),
        explode(',', (string) env('GEO_ALLOWED_COUNTRIES', 'GE')),
    ))),
    'block_unknown' => (bool) env('GEO_BLOCK_UNKNOWN_COUNTRY', true),
    'country_headers' => [
        'cf-ipcountry',
        'x-country-code',
        'x-vercel-ip-country',
        'cloudfront-viewer-country',
        'fastly-client-geo-country-code',
        'x-appengine-country',
        'x-geo-country',
        'x-forwarded-country',
    ],
];
