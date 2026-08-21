<?php

namespace App\Notifications;

use App\Models\ContactLead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class NewContactLeadNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ContactLead $lead) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fullName = trim($this->lead->name ?: trim($this->lead->first_name.' '.$this->lead->last_name));
        $subjectDetail = $fullName ?: $this->lead->phone;
        $mailMessage = (new MailMessage)
            ->subject('ახალი საკონსულტაციო მოთხოვნა'.($subjectDetail ? ': '.$subjectDetail : ''))
            ->greeting('ახალი საკონსულტაციო მოთხოვნა')
            ->lines(array_filter([
                'წყარო: '.$this->sourceLabel(),
                $fullName !== '' ? 'სახელი: '.$fullName : null,
                filled($this->lead->company) ? 'კომპანია: '.$this->lead->company : null,
                filled($this->lead->phone) ? 'ტელეფონი: '.$this->lead->phone : null,
                filled($this->lead->email) ? 'ელფოსტა: '.$this->lead->email : null,
                filled($this->lead->address) ? 'მისამართი: '.$this->lead->address : null,
                filled($this->lead->service) ? 'სერვისი: '.$this->lead->service : null,
                filled($this->lead->project_size) ? 'პროექტის ზომა: '.$this->lead->project_size : null,
                filled($this->lead->property_type) ? 'ობიექტის ტიპი: '.$this->lead->property_type : null,
            ]));

        if (filled($this->lead->message)) {
            $mailMessage->line('მოთხოვნა: '.Str::limit((string) $this->lead->message, 600));
        }

        collect($this->lead->details ?? [])
            ->filter(fn (mixed $detail): bool => is_array($detail))
            ->map(function (array $detail): ?string {
                $label = trim((string) data_get($detail, 'label', data_get($detail, 'key', '')));
                $value = trim((string) data_get($detail, 'value', ''));

                return $label !== '' && $value !== ''
                    ? $label.': '.Str::limit($value, 240)
                    : null;
            })
            ->filter()
            ->take(8)
            ->each(fn (string $line) => $mailMessage->line($line));

        $mailMessage
            ->line('მიღებულია: '.$this->lead->created_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'))
            ->salutation('SafeTech');

        if (filter_var($this->lead->email, FILTER_VALIDATE_EMAIL)) {
            $mailMessage->replyTo($this->lead->email, $fullName ?: null);
        }

        return $mailMessage;
    }

    private function sourceLabel(): string
    {
        return match ($this->lead->source) {
            'contact-page' => 'კონტაქტის გვერდი',
            'home-cta' => 'მთავარი გვერდი',
            'consultation-popup' => 'კონსულტაციის ფანჯარა',
            default => $this->lead->source,
        };
    }
}
