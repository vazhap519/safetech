<?php

return [
    'notification_email' => env(
        'LEADS_NOTIFICATION_EMAIL',
        'info@safetech.ge',
    ),
    'high_priority_email' => env('LEADS_HIGH_PRIORITY_EMAIL'),
    'high_priority_threshold' => max(1, min(100, (int) env('LEADS_HIGH_PRIORITY_THRESHOLD', 70))),
    'crm_webhook_url' => env('CRM_WEBHOOK_URL'),
    'crm_webhook_token' => env('CRM_WEBHOOK_TOKEN'),
    'crm_connect_timeout' => max(1, (int) env('CRM_CONNECT_TIMEOUT', 3)),
    'crm_timeout' => max(1, (int) env('CRM_TIMEOUT', 10)),
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'connect_timeout' => max(1, (int) env('TELEGRAM_CONNECT_TIMEOUT', 3)),
        'timeout' => max(1, (int) env('TELEGRAM_TIMEOUT', 10)),
    ],
];
