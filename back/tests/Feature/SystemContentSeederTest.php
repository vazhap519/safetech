<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\SystemContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_preserves_existing_admin_managed_contact_values(): void
    {
        SiteSetting::query()->create([
            'key' => 'contact',
            'group' => 'general',
            'is_public' => true,
            'value' => [
                'phone' => '555 000 111',
                'phones' => ['555 000 111'],
                'whatsapp' => '555000111',
                'whatsapp_message' => 'Custom WhatsApp message',
                'whatsapp_enabled' => false,
                'email' => 'custom@example.com',
            ],
        ]);

        $this->seed(SystemContentSeeder::class);

        $contact = SiteSetting::query()->where('key', 'contact')->sole()->value;

        $this->assertSame('555 000 111', $contact['phone']);
        $this->assertSame(['555 000 111'], $contact['phones']);
        $this->assertSame('555000111', $contact['whatsapp']);
        $this->assertSame('Custom WhatsApp message', $contact['whatsapp_message']);
        $this->assertFalse($contact['whatsapp_enabled']);
        $this->assertSame('custom@example.com', $contact['email']);
    }
}
