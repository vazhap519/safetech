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
        config()->set('cms.admin.email', 'admin@example.com');

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $service = Service::query()->create([
            'name' => 'Notification test service',
            'title' => 'Notification test service',
            'slug' => 'notification-test-service',
            'description' => 'Notification test service description.',
            'seo_description' => 'Notification test service description.',
            'is_published' => true,
        ]);

        $response = $this->postJson('/api/contact-leads', [
            'firstName' => 'Notification',
            'lastName' => 'Customer',
            'phone' => '+995555123456',
            'email' => 'notification@example.com',
            'address' => 'Tbilisi',
            'serviceSlug' => $service->slug,
            'message' => 'I need a notification test consultation.',
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
