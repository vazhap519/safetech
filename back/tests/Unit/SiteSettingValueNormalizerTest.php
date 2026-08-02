<?php

namespace Tests\Unit;

use App\Support\SiteSettingValueNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SiteSettingValueNormalizerTest extends TestCase
{
    #[Test]
    public function it_normalizes_enabled_social_profiles_and_dynamic_share_buttons(): void
    {
        $normalized = SiteSettingValueNormalizer::normalize('socials', [
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

        $this->assertSame([
            [
                'network' => 'facebook',
                'label' => 'Facebook',
                'href' => 'facebook.com/safetech',
                'enabled' => true,
                'open_in_new_tab' => false,
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
        ], $normalized['share_buttons']);
    }
}
