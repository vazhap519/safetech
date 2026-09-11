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

        // The CMS recipient is intentional, editable business data. The
        // environment value remains a safe deployment fallback when no valid
        // address has been saved in the Contact settings yet.
        $recipient = filter_var($settingsRecipient, FILTER_VALIDATE_EMAIL)
            ? $settingsRecipient
            : (filter_var($configuredRecipient, FILTER_VALIDATE_EMAIL)
                ? $configuredRecipient
                : (string) config('mail.from.address'));

        $recipients = [$recipient];
        $priorityRecipient = trim((string) config('leads.high_priority_email'));

        if ($event->lead->lead_score >= (int) config('leads.high_priority_threshold', 70)
            && filter_var($priorityRecipient, FILTER_VALIDATE_EMAIL)) {
            $recipients[] = $priorityRecipient;
        }

        foreach (array_unique($recipients) as $email) {
            Notification::route('mail', $email)
                ->notify(new NewContactLeadNotification($event->lead));
        }
    }
}
