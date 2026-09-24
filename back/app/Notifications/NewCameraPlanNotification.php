<?php

namespace App\Notifications;

use App\Models\CameraPlan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class NewCameraPlanNotification extends Notification
{
    public function __construct(private readonly CameraPlan $plan) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->plain($this->plan->contact_name, 100);
        $title = $this->plain($this->plan->title, 140);
        $phone = $this->plain($this->plan->contact_phone, 24);
        $email = $this->plain($this->plan->contact_email, 160);
        $count = count($this->plan->layout['cameras'] ?? []);

        $mail = (new MailMessage)
            ->subject('ახალი კამერების გეგმა #'.$this->plan->getKey().' | SafeTech')
            ->greeting('ახალი მოთხოვნა კამერების პლანერიდან')
            ->line('ობიექტი: '.$title)
            ->line('კლიენტი: '.$name)
            ->line('ტელეფონი: '.$phone)
            ->line('კამერების რაოდენობა (წინასწარი გეგმა): '.$count);

        if ($email !== '') {
            $mail->line('ელფოსტა: '.$email);
        }

        $mail->line('მიღებულია: '.$this->plan->created_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'))
            ->action('გეგმის ნახვა დაცულ ადმინისტრატორის გვერდზე', route('admin.camera-plans.layout', $this->plan))
            ->line('გეგმის ფოტო და დეტალური მონაცემები წერილს არ ერთვის. მათზე წვდომა შესაძლებელია მხოლოდ ავტორიზებულ ადმინისტრატორს.')
            ->salutation('SafeTech');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($email, $name ?: null);
        }

        return $mail;
    }

    private function plain(?string $value, int $limit): string
    {
        // Keep untrusted form text within a single notification line.
        return Str::limit(preg_replace('/[\r\n\t]+/u', ' ', trim((string) $value)) ?? '', $limit, '');
    }
}
