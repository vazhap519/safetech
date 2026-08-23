<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_is_published_and_localized(): void
    {
        $this->getJson('/api/pages/privacy?locale=en')
            ->assertOk()
            ->assertJsonPath('data.slug', 'privacy')
            ->assertJsonPath('data.title', 'Privacy Policy')
            ->assertJsonPath('data.seo.noindex', false);

        $this->getJson('/api/pages/privacy?locale=ka')
            ->assertOk()
            ->assertJsonPath('data.title', 'კონფიდენციალურობის პოლიტიკა');
    }

    public function test_terms_are_published_and_localized(): void
    {
        $this->getJson('/api/pages/terms?locale=en')
            ->assertOk()
            ->assertJsonPath('data.slug', 'terms')
            ->assertJsonPath('data.title', 'Terms of Service')
            ->assertJsonPath('data.seo.noindex', false);

        $this->getJson('/api/pages/terms?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.title', 'Условия использования');
    }
}
