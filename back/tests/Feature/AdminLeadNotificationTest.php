<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLeadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_contact_lead_creates_an_unread_admin_notification(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $service = Service::query()->create([
            'title' => 'Notification test service',
            'slug' => 'notification-test-service',
            'short_description' => 'Notification test service description.',
        ]);

        $response = $this->postJson('/api/contact-leads', [
            'firstName' => 'Notification',
            'lastName' => 'Customer',
            'phone' => '+995555123456',
            'serviceSlug' => $service->slug,
            'source' => 'contact-page',
            'privacy' => true,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->getKey(),
            'read_at' => null,
        ]);

        $notification = $admin->notifications()->latest()->firstOrFail();
        $payload = $notification->data;

        $this->assertSame('ახალი მოთხოვნა', data_get($payload, 'title'));
        $this->assertStringContainsString('Notification Customer', (string) data_get($payload, 'body'));
        $this->assertStringContainsString('+995555123456', (string) data_get($payload, 'body'));
    }
}
