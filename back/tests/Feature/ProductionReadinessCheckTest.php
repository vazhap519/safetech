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
        $this->configureHardenedProduction();

        $this->artisan('cms:production-check')
            ->assertSuccessful()
            ->expectsOutputToContain('Production configuration is ready for deployment.');
    }

    public function test_it_rejects_a_mailer_that_can_silently_fall_back_to_logs(): void
    {
        $this->configureHardenedProduction();
        config()->set([
            'mail.default' => 'failover',
            'mail.mailers.failover.mailers' => ['smtp', 'log'],
        ]);

        $this->artisan('cms:production-check')
            ->assertFailed()
            ->expectsOutputToContain('[FAIL] Outbound mailer does not fall back to logs');
    }

    public function test_it_rejects_an_unsupported_smtp_scheme(): void
    {
        $this->configureHardenedProduction();
        config()->set('mail.mailers.smtp.scheme', 'tls');

        $this->artisan('cms:production-check')
            ->assertFailed()
            ->expectsOutputToContain('[FAIL] SMTP scheme is supported');
    }

    private function configureHardenedProduction(): void
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
            'cms.admin.email' => 'safetechgeorgia@gmail.com',
            'cms.admin.password' => 'initial-password-123',
            'leads.notification_email' => 'safetechgeorgia@gmail.com',
            'mail.default' => 'smtp',
            'mail.from.address' => 'safetechgeorgia@gmail.com',
            'mail.mailers.smtp.host' => 'smtp.gmail.com',
            'mail.mailers.smtp.scheme' => 'smtp',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.username' => 'safetechgeorgia@gmail.com',
            'mail.mailers.smtp.password' => 'provider-app-password',
        ]);
    }
}
