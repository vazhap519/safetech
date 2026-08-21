<?php

namespace Tests\Unit;

use App\Support\TeamMemberSocialLinks;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamMemberSocialLinksTest extends TestCase
{
    #[Test]
    public function it_normalizes_legacy_and_selectable_team_social_profiles(): void
    {
        $socials = TeamMemberSocialLinks::normalize([
            'linkedin' => 'https://linkedin.com/in/safetech',
            ['network' => 'twitter', 'href' => 'https://x.com/safetech'],
            ['network' => 'whatsapp', 'href' => '+995 599 123 456'],
            ['network' => 'linkedin', 'href' => 'https://linkedin.com/in/duplicate'],
            ['network' => 'unknown', 'href' => 'https://example.com'],
            ['network' => 'facebook', 'href' => 'javascript:alert(1)'],
        ]);

        $this->assertSame([
            'linkedin' => 'https://linkedin.com/in/safetech',
            'x' => 'https://x.com/safetech',
            'whatsapp' => '+995 599 123 456',
        ], $socials);
    }

    #[Test]
    public function it_converts_saved_social_profiles_to_selectable_form_rows(): void
    {
        $rows = TeamMemberSocialLinks::formRows([
            'facebook' => 'https://facebook.com/safetech',
            'email' => 'team@safetech.ge',
        ]);

        $this->assertSame([
            ['network' => 'facebook', 'href' => 'https://facebook.com/safetech'],
            ['network' => 'email', 'href' => 'team@safetech.ge'],
        ], $rows);
    }
}
