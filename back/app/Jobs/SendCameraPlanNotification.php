<?php

namespace App\Jobs;

use App\Models\CameraPlan;
use App\Models\SiteSetting;
use App\Notifications\NewCameraPlanNotification;
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

    public function __construct(public readonly int $cameraPlanId)
    {
    }

    public function handle(): void
    {
        $plan = CameraPlan::query()->find($this->cameraPlanId);

        if (!$plan) {
            return;
        }

        $contactSettings = SiteSetting::query()
            ->where('key', 'contact')
            ->first()?->value ?? [];

        $settingsRecipient = trim((string) data_get($contactSettings, 'lead_email', ''));
        $configuredRecipient = trim((string) config('leads.notification_email'));
        $fallback = trim((string) config('mail.from.address'));

        $recipient = collect([$settingsRecipient, $configuredRecipient, $fallback])
            ->first(fn (string $address): bool => filter_var($address, FILTER_VALIDATE_EMAIL) !== false);

        if (!$recipient) {
            Log::warning('Camera plan notification has no valid recipient.', ['plan_id' => $plan->getKey()]);

            return;
        }

        Notification::route('mail', $recipient)
            ->notify(new NewCameraPlanNotification($plan));
    }
}
