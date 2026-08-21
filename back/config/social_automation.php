<?php

return [
    'project_publish' => [
        'enabled' => filter_var(
            env('N8N_PROJECT_PUBLISH_ENABLED', env('APP_ENV') === 'production'),
            FILTER_VALIDATE_BOOL,
        ),
        'webhook_url' => env(
            'N8N_PROJECT_PUBLISH_WEBHOOK_URL',
            'https://n8n.safetech.ge/webhook/project-published',
        ),
        'token' => env('N8N_PROJECT_PUBLISH_WEBHOOK_TOKEN'),
        'connect_timeout' => (int) env('N8N_PROJECT_PUBLISH_CONNECT_TIMEOUT', 3),
        'timeout' => (int) env('N8N_PROJECT_PUBLISH_TIMEOUT', 30),
    ],
];
