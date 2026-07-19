<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_settings_endpoint_reads_the_canonical_site_settings(): void
    {
        foreach ([
            'contact' => ['phone' => '+995 555 00 00 00', 'email' => 'info@example.com', 'address' => 'Tbilisi'],
            'branding' => ['site_name' => 'SafeTech', 'tagline' => 'Secure infrastructure'],
            'seo' => ['city' => 'Tbilisi', 'country' => 'GE', 'open_time' => '09:00', 'close_time' => '18:00'],
            'socials' => [
                'links' => [['network' => 'facebook', 'label' => 'Facebook', 'href' => 'facebook.com/safetech']],
                'share_title' => 'Share',
                'share_buttons' => ['facebook', 'link'],
            ],
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'general',
                    'value' => $value,
                    'is_public' => true,
                ],
            );
        }

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('contact.phone', '+995 555 00 00 00')
            ->assertJsonPath('seo.local_business.city', 'Tbilisi')
            ->assertJsonPath('seo.same_as.0', 'https://facebook.com/safetech')
            ->assertJsonPath('share.buttons.0.type', 'facebook');
    }
}
