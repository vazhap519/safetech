<?php

return [
    'notification_email' => env(
        'LEADS_NOTIFICATION_EMAIL',
        env('ADMIN_EMAIL', 'safetechgeorgia@gmail.com'),
    ),
    'crm_webhook_url' => env('CRM_WEBHOOK_URL'),
    'crm_webhook_token' => env('CRM_WEBHOOK_TOKEN'),
];
