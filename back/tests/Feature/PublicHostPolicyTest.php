<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicHostPolicyTest extends TestCase
{
    public function test_backend_entry_point_reports_a_healthy_service(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_backend_robots_policy_blocks_all_crawlers(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, nosnippet')
            ->assertSeeText('User-agent: *')
            ->assertSeeText('Disallow: /');
    }
}
