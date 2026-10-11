<?php

namespace App\Support\Leads;

use App\Models\SiteSetting;

final class LeadNotificationRecipient
{
    public function resolve(): ?string
    {
        $contact = SiteSetting::query()->where('key', 'contact')->first()?->value ?? [];

        foreach ([
            data_get($contact, 'lead_email'),
            config('leads.notification_email'),
            config('mail.from.address'),
        ] as $candidate) {
            $email = trim((string) $candidate);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        return null;
    }
}
