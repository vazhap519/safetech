<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Models\SiteSetting;
use App\Notifications\NewContactLeadNotification;
use Illuminate\Support\Facades\Notification;

final class SendLeadNotification
{
    public function handle(LeadCreated $event): void
    {
        $contactSettings = SiteSetting::query()
            ->where('key', 'contact')
            ->first()?->value ?? [];

        $configuredRecipient = trim((string) config('leads.notification_email'));
        $settingsRecipient = trim((string) data_get($contactSettings, 'lead_email', ''));

        $recipient = filter_var($configuredRecipient, FILTER_VALIDATE_EMAIL)
            ? $configuredRecipient
            : (filter_var($settingsRecipient, FILTER_VALIDATE_EMAIL)
                ? $settingsRecipient
                : (string) config('mail.from.address'));

        Notification::route('mail', $recipient)
            ->notify(new NewContactLeadNotification($event->lead));
    }
}
