<?php

namespace Tests\Feature;

use Illuminate\Encryption\Encrypter;
use Tests\TestCase;

class ProductionReadinessCheckTest extends TestCase
{
    public function test_it_rejects_non_production_configuration(): void
    {
        $this->artisan('cms:production-check')
            ->assertFailed()
            ->expectsOutputToContain('Production readiness check failed');
    }

    public function test_it_accepts_hardened_production_configuration(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        config()->set([
            'app.debug' => false,
            'app.key' => 'base64:'.base64_encode(Encrypter::generateKey('AES-256-CBC')),
            'app.cipher' => 'AES-256-CBC',
            'app.url' => 'https://api.safetech.ge',
            'app.frontend_url' => 'https://safetech.ge',
            'app.revalidate_secret' => str_repeat('a', 32),
            'app.seed_demo_content' => false,
            'database.default' => 'pgsql',
            'database.connections.pgsql.driver' => 'pgsql',
            'database.connections.pgsql.password' => 'test-password',
            'session.driver' => 'database',
            'session.encrypt' => true,
            'session.secure' => true,
            'queue.default' => 'database',
            'cache.default' => 'database',
            'mail.default' => 'smtp',
        ]);

        $this->artisan('cms:production-check')
            ->assertSuccessful()
            ->expectsOutputToContain('Production configuration is ready for deployment.');
    }
}
