<?php

namespace Tests\Feature;

use App\Notifications\NewContactLeadNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_routes_new_leads_to_the_configured_business_email(): void
    {
        Notification::fake();
        config()->set('leads.notification_email', 'safetechgeorgia@gmail.com');

        $this->postJson('/api/contact-leads', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'message' => 'Test request',
            'source' => 'contact-page',
            'privacy' => true,
        ])->assertCreated();

        Notification::assertSentOnDemand(
            NewContactLeadNotification::class,
            static fn (
                NewContactLeadNotification $notification,
                array $channels,
                AnonymousNotifiable $notifiable,
            ): bool => $channels === ['mail']
                && $notifiable->routeNotificationFor('mail') === 'safetechgeorgia@gmail.com',
        );
    }
}
