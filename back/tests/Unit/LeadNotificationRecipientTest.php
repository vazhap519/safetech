<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use App\Support\Leads\LeadNotificationRecipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadNotificationRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_recipient_takes_precedence(): void
    {
        config()->set('leads.notification_email', 'fallback@example.com');
        SiteSetting::query()->create([
            'key' => 'contact',
            'group' => 'general',
            'is_public' => true,
            'value' => ['lead_email' => '  cms@example.com  '],
        ]);

        $this->assertSame('cms@example.com', app(LeadNotificationRecipient::class)->resolve());
    }

    public function test_invalid_cms_recipient_falls_back_to_environment(): void
    {
        config()->set('leads.notification_email', 'fallback@example.com');
        SiteSetting::query()->create([
            'key' => 'contact',
            'group' => 'general',
            'is_public' => true,
            'value' => ['lead_email' => 'invalid'],
        ]);

        $this->assertSame('fallback@example.com', app(LeadNotificationRecipient::class)->resolve());
    }

    public function test_invalid_recipients_return_null(): void
    {
        config()->set('leads.notification_email', 'invalid');
        config()->set('mail.from.address', 'invalid');

        $this->assertNull(app(LeadNotificationRecipient::class)->resolve());
    }
}
