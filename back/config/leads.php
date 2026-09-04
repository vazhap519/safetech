<?php

return [
    'notification_email' => env(
        'LEADS_NOTIFICATION_EMAIL',
        'info@safetech.ge',
    ),
    'crm_webhook_url' => env('CRM_WEBHOOK_URL'),
    'crm_webhook_token' => env('CRM_WEBHOOK_TOKEN'),
];
