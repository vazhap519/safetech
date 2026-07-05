<?php

namespace App\Notifications;

use App\Models\ContactLead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $fullName = $this->lead->name ?: trim($this->lead->first_name.' '.$this->lead->last_name);
        $mailMessage = (new MailMessage)
            ->subject('ახალი მოთხოვნა SafeTech-ის საიტიდან')
            ->greeting('ახალი მოთხოვნა')
            ->line('წყარო: '.$this->lead->source)
            ->line('სახელი: '.($fullName ?: '—'))
            ->line('კომპანია: '.($this->lead->company ?: '—'))
            ->line('ტელეფონი: '.($this->lead->phone ?: '—'))
            ->line('ელფოსტა: '.($this->lead->email ?: '—'))
            ->line('სერვისი: '.($this->lead->service ?: '—'))
            ->line('პროექტის ზომა: '.($this->lead->project_size ?: '—'))
            ->line('ობიექტის ტიპი: '.($this->lead->property_type ?: '—'))
            ->line('შეტყობინება: '.($this->lead->message ?: '—'))
            ->line('შექმნის დრო: '.$this->lead->created_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i:s'));

        if ($this->lead->email) {
            $mailMessage->replyTo($this->lead->email, $fullName ?: null);
        }

        return $mailMessage;
    }
}
