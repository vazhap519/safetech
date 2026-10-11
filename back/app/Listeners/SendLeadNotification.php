<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Support\Leads\LeadNotificationRecipient;
use Illuminate\Support\Facades\Log;
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
        $recipient = app(LeadNotificationRecipient::class)->resolve();

        if ($recipient === null) {
            Log::error('Lead notification has no valid recipient.', ['lead_id' => $event->lead->getKey()]);
            throw new \RuntimeException('Lead notification has no valid recipient.');
        }

        $recipients = [$recipient];
        $priorityRecipient = trim((string) config('leads.high_priority_email'));

        if ($event->lead->lead_score >= (int) config('leads.high_priority_threshold', 70)
            && filter_var($priorityRecipient, FILTER_VALIDATE_EMAIL)) {
            $recipients[] = $priorityRecipient;
        }

        foreach (array_unique($recipients) as $email) {
            try {
                Notification::route('mail', $email)
                    ->notify(new NewContactLeadNotification($event->lead));

                // SMTP acceptance is not proof of final inbox delivery.
                Log::info('Lead notification accepted by mail transport.', [
                    'lead_id' => $event->lead->getKey(),
                    'recipient' => $email,
                    'mailer' => config('mail.default'),
                ]);
            } catch (\Throwable $exception) {
                Log::error('Lead notification mail transport failed.', [
                    'lead_id' => $event->lead->getKey(),
                    'recipient' => $email,
                    'exception' => $exception::class,
                ]);

                throw $exception;
            }
        }
    }
}
