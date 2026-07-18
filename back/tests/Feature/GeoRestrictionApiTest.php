<?php

namespace Tests\Feature;

use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GeoRestrictionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function enableGeoRestriction(): void
    {
        Config::set('geo.enabled', true);
        Config::set('geo.allowed_countries', ['GE']);
        Config::set('geo.block_unknown', true);
    }

    public function test_it_allows_public_api_requests_from_georgia(): void
    {
        $this->seed(ContentSeeder::class);
        $this->enableGeoRestriction();

        $this->withHeader('X-Country-Code', 'GE')
            ->getJson('/api/services')
            ->assertOk();
    }

    public function test_it_rejects_public_api_requests_from_other_countries(): void
    {
        $this->enableGeoRestriction();

        $this->withHeader('X-Country-Code', 'US')
            ->getJson('/api/services')
            ->assertForbidden()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertJsonPath('message', 'This content is available in Georgia only.');
    }
}
