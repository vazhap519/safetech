<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class RequestIdTest extends TestCase
{
    public function test_api_generates_a_request_id_when_missing(): void
    {
        $requestId = $this->getJson('/api/health')
            ->assertHeader('X-Request-ID')
            ->headers->get('X-Request-ID');

        $this->assertTrue(Str::isUuid($requestId));
    }

    public function test_api_preserves_a_valid_upstream_request_id(): void
    {
        $requestId = (string) Str::uuid();

        $this->withHeader('X-Request-ID', $requestId)
            ->getJson('/api/health')
            ->assertHeader('X-Request-ID', $requestId);
    }

    public function test_api_replaces_an_untrusted_request_id(): void
    {
        $response = $this->withHeader('X-Request-ID', 'not-a-uuid')
            ->getJson('/api/health');

        $this->assertNotSame('not-a-uuid', $response->headers->get('X-Request-ID'));
        $this->assertTrue(Str::isUuid($response->headers->get('X-Request-ID')));
    }
}
