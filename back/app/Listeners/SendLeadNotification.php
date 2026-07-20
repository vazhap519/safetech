<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Models\SiteSetting;
use App\Notifications\NewContactLeadNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

final class SendLeadNotification implements ShouldQueue
{
    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 180];

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
