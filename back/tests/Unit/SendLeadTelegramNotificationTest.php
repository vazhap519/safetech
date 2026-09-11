<?php

namespace Tests\Unit;

use App\Events\LeadCreated;
use App\Listeners\SendLeadTelegramNotification;
use App\Models\ContactLead;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SendLeadTelegramNotificationTest extends TestCase
{
    public function test_it_does_not_call_telegram_without_complete_credentials(): void
    {
        Http::fake();
        config()->set('leads.telegram.bot_token', '');
        config()->set('leads.telegram.chat_id', '');

        (new SendLeadTelegramNotification)->handle(new LeadCreated($this->lead()));

        Http::assertNothingSent();
    }

    public function test_it_sends_the_lead_and_calculator_details_to_the_configured_chat(): void
    {
        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);
        config()->set('leads.telegram.bot_token', 'secret-token');
        config()->set('leads.telegram.chat_id', '-100123');

        (new SendLeadTelegramNotification)->handle(new LeadCreated($this->lead()));

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.telegram.org/botsecret-token/sendMessage'
                && $payload['chat_id'] === '-100123'
                && str_contains($payload['text'], 'SafeTech — ახალი მოთხოვნა #42')
                && str_contains($payload['text'], 'კამერები')
                && str_contains($payload['text'], '"total":1909');
        });
    }

    private function lead(): ContactLead
    {
        $lead = new ContactLead([
            'name' => 'ტესტ მომხმარებელი',
            'phone' => '+995555123456',
            'service' => 'კამერები',
            'message' => 'მჭირდება შეთავაზება',
            'details' => ['total' => 1909],
            'source' => 'calculator',
        ]);
        $lead->id = 42;

        return $lead;
    }
}
