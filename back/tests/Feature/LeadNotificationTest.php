<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SiteSetting;
use App\Notifications\NewContactLeadNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_routes_complete_consultations_to_the_configured_business_email_without_blank_rows(): void
    {
        Notification::fake();
        config()->set('leads.notification_email', 'fallback@example.com');

        SiteSetting::query()->create([
            'key' => 'contact',
            'group' => 'general',
            'is_public' => true,
            'value' => ['lead_email' => 'info@safetech.ge'],
        ]);

        $service = Service::query()->create([
            'slug' => 'it-support',
            'name' => 'IT support',
            'title' => 'IT support',
            'description' => 'On-site IT support.',
            'seo_description' => 'On-site IT support.',
            'is_published' => true,
        ]);

        $this->postJson('/api/contact-leads', [
            'firstName' => 'Test',
            'lastName' => 'Customer',
            'phone' => '+995555123456',
            'email' => 'customer@example.com',
            'address' => 'Tbilisi',
            'serviceSlug' => $service->slug,
            'message' => 'I need a consultation for an office IT project.',
            'source' => 'consultation-popup',
            'privacy' => true,
        ])->assertCreated();

        Notification::assertSentOnDemand(
            NewContactLeadNotification::class,
            static function (
                NewContactLeadNotification $notification,
                array $channels,
                AnonymousNotifiable $notifiable,
            ): bool {
                $mail = $notification->toMail($notifiable);
                $rendered = implode("\n", array_map('strval', $mail->introLines));

                return $channels === ['mail']
                    && $notifiable->routeNotificationFor('mail') === 'info@safetech.ge'
                    && str_contains((string) $mail->subject, 'Test Customer')
                    && str_contains($rendered, 'სახელი: Test Customer')
                    && str_contains($rendered, 'ელფოსტა: customer@example.com')
                    && str_contains($rendered, 'სერვისი: IT support')
                    && ! str_contains($rendered, 'კომპანია:')
                    && ! str_contains($rendered, '—');
            },
        );
    }
}
