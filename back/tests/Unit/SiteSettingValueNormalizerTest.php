<?php

namespace Tests\Unit;

use App\Support\SiteSettingValueNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SiteSettingValueNormalizerTest extends TestCase
{
    #[Test]
    public function it_normalizes_whatsapp_contact_settings(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('contact', [
            'whatsapp' => ' +995 599 12 34 56 ',
            'whatsapp_enabled' => 'false',
            'whatsapp_message' => 'Hello from SafeTech',
        ]);

        $this->assertSame('+995 599 12 34 56', $normalized['whatsapp']);
        $this->assertFalse($normalized['whatsapp_enabled']);
        $this->assertSame('Hello from SafeTech', $normalized['whatsapp_message']);
        $this->assertSame('info@safetech.ge', $normalized['lead_email']);
    }

    #[Test]
    public function it_normalizes_social_profiles_and_dynamic_share_buttons_without_losing_disabled_rows(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('socials', [
            'links_managed' => true,
            'links' => [
                [
                    'network' => 'facebook',
                    'label' => '',
                    'href' => 'facebook.com/safetech',
                    'enabled' => true,
                    'open_in_new_tab' => false,
                ],
                [
                    'network' => 'instagram',
                    'href' => 'instagram.com/hidden',
                    'enabled' => false,
                ],
                [
                    'network' => 'viber',
                    'label' => 'Viber support',
                    'href' => '+995599123456',
                ],
            ],
            'share_enabled' => 'true',
            'share_on_services' => 1,
            'share_on_projects' => 'false',
            'share_title_ka' => ' გაზიარება ',
            'share_buttons' => [
                ['type' => 'facebook', 'enabled' => true],
                ['type' => 'twitter', 'label' => 'Post on X'],
                ['type' => 'link'],
                ['type' => 'telegram', 'enabled' => false],
                ['type' => 'facebook'],
            ],
        ]);

        $this->assertTrue($normalized['links_managed']);
        $this->assertSame([
            [
                'network' => 'facebook',
                'label' => 'Facebook',
                'href' => 'facebook.com/safetech',
                'enabled' => true,
                'open_in_new_tab' => false,
            ],
            [
                'network' => 'instagram',
                'label' => 'Instagram',
                'href' => 'instagram.com/hidden',
                'enabled' => false,
                'open_in_new_tab' => true,
            ],
            [
                'network' => 'viber',
                'label' => 'Viber support',
                'href' => '+995599123456',
                'enabled' => true,
                'open_in_new_tab' => true,
            ],
        ], $normalized['links']);
        $this->assertTrue($normalized['share_enabled']);
        $this->assertTrue($normalized['share_on_services']);
        $this->assertFalse($normalized['share_on_projects']);
        $this->assertSame('გაზიარება', $normalized['share_title_ka']);
        $this->assertSame([
            ['type' => 'facebook', 'label' => '', 'enabled' => true],
            ['type' => 'x', 'label' => 'Post on X', 'enabled' => true],
            ['type' => 'copy', 'label' => '', 'enabled' => true],
            ['type' => 'telegram', 'label' => '', 'enabled' => false],
        ], $normalized['share_buttons']);
    }

    #[Test]
    public function canonical_social_links_make_repeater_deletions_persistent(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('socials', [
            'links_managed' => true,
            'facebook' => 'https://facebook.com/old-safetech',
            'instagram' => 'https://instagram.com/old-safetech',
            'links' => [
                [
                    'network' => 'instagram',
                    'href' => 'https://instagram.com/safetech-new',
                    'enabled' => true,
                    'open_in_new_tab' => true,
                ],
            ],
        ]);

        $this->assertArrayNotHasKey('facebook', $normalized);
        $this->assertArrayNotHasKey('instagram', $normalized);
        $this->assertCount(1, $normalized['links']);
        $this->assertSame('instagram', $normalized['links'][0]['network']);
        $this->assertSame('https://instagram.com/safetech-new', $normalized['links'][0]['href']);
    }

    #[Test]
    public function it_migrates_legacy_social_keys_even_when_seeders_added_an_empty_links_array(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('socials', [
            'links' => [],
            'facebook' => 'https://facebook.com/safetech',
            'youtube' => 'https://youtube.com/@safetech',
        ]);

        $this->assertTrue($normalized['links_managed']);
        $this->assertArrayNotHasKey('facebook', $normalized);
        $this->assertArrayNotHasKey('youtube', $normalized);
        $this->assertSame(['facebook', 'youtube'], array_column($normalized['links'], 'network'));
    }

    #[Test]
    public function deleting_every_social_profile_does_not_restore_legacy_values(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('socials', [
            'links_managed' => true,
            'facebook' => 'https://facebook.com/legacy',
            'links' => [],
        ]);

        $this->assertSame([], $normalized['links']);
        $this->assertArrayNotHasKey('facebook', $normalized);
    }
}
