<?php

namespace App\Jobs;

use App\Models\CameraPlan;
use App\Notifications\NewCameraPlanNotification;
use App\Support\Leads\LeadNotificationRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

final class SendCameraPlanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 180];

    public function __construct(public readonly int $cameraPlanId) {}

    public function handle(): void
    {
        $plan = CameraPlan::query()->find($this->cameraPlanId);

        if ($plan === null) {
            return;
        }

        $recipient = app(LeadNotificationRecipient::class)->resolve();

        if ($recipient === null) {
            Log::error('Camera plan notification has no valid recipient.', ['plan_id' => $plan->getKey()]);
            throw new \RuntimeException('Camera plan notification has no valid recipient.');
        }

        try {
            Notification::route('mail', $recipient)
                ->notify(new NewCameraPlanNotification($plan));

            Log::info('Camera plan notification accepted by mail transport.', [
                'plan_id' => $plan->getKey(),
                'recipient' => $recipient,
                'mailer' => config('mail.default'),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Camera plan notification mail transport failed.', [
                'plan_id' => $plan->getKey(),
                'recipient' => $recipient,
                'exception' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
