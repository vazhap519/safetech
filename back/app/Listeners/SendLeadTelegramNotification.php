<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

final class SendLeadTelegramNotification implements ShouldQueue
{
    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 180];

    public function handle(LeadCreated $event): void
    {
        $botToken = trim((string) config('leads.telegram.bot_token'));
        $chatId = trim((string) config('leads.telegram.chat_id'));

        if ($botToken === '' || $chatId === '') {
            return;
        }

        try {
            Http::asJson()
                ->connectTimeout((int) config('leads.telegram.connect_timeout', 3))
                ->timeout((int) config('leads.telegram.timeout', 10))
                ->retry(2, 250)
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $this->message($event),
                    'disable_web_page_preview' => true,
                ])
                ->throw();
        } catch (Throwable) {
            // HTTP client exceptions include the request URL, which contains
            // Telegram's bot token. Keep the queued failure retryable without
            // persisting that credential in failed-job traces.
            throw new RuntimeException('Telegram lead notification failed.');
        }
    }

    private function message(LeadCreated $event): string
    {
        $lead = $event->lead;
        $details = is_array($lead->details) && $lead->details !== []
            ? json_encode($lead->details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : null;

        $lines = array_filter([
            'SafeTech — ახალი მოთხოვნა #'.$lead->getKey(),
            filled($lead->name) ? 'სახელი: '.$lead->name : null,
            filled($lead->company) ? 'კომპანია: '.$lead->company : null,
            filled($lead->phone) ? 'ტელეფონი: '.$lead->phone : null,
            filled($lead->email) ? 'ელფოსტა: '.$lead->email : null,
            filled($lead->service) ? 'სერვისი: '.$lead->service : null,
            filled($lead->message) ? 'აღწერა: '.$lead->message : null,
            $details ? 'კალკულაცია: '.$details : null,
            'წყარო: '.$lead->source,
        ]);

        return mb_substr(implode("\n", $lines), 0, 4000);
    }
}
