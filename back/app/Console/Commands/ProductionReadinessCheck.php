<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;

class ProductionReadinessCheck extends Command
{
    protected $signature = 'cms:production-check';

    protected $description = 'Validate security-critical production configuration before deployment';

    public function handle(): int
    {
        $connection = (string) config('database.default');
        $database = config("database.connections.{$connection}", []);
        $appKey = (string) config('app.key', '');
        $parsedAppKey = str_starts_with($appKey, 'base64:')
            ? (base64_decode(substr($appKey, 7), true) ?: '')
            : $appKey;
        $adminEmail = trim((string) config('cms.admin.email'));
        $adminPassword = (string) config('cms.admin.password');
        $leadEmail = trim((string) config('leads.notification_email'));
        $mailFrom = trim((string) config('mail.from.address'));
        $defaultMailer = (string) config('mail.default');
        $mailers = (array) config('mail.mailers', []);
        $defaultMailerConfig = (array) ($mailers[$defaultMailer] ?? []);
        $mailerTransports = $defaultMailer === 'failover'
            ? (array) ($defaultMailerConfig['mailers'] ?? [])
            : [$defaultMailer];
        $smtpConfig = (array) config('mail.mailers.smtp', []);
        $usesSmtp = in_array('smtp', $mailerTransports, true);
        $smtpConfigured = filled($smtpConfig['url'] ?? null) || (
            filled($smtpConfig['host'] ?? null)
            && filled($smtpConfig['port'] ?? null)
            && filled($smtpConfig['username'] ?? null)
            && filled($smtpConfig['password'] ?? null)
        );
        $deliveryMailerConfigured = $defaultMailer !== ''
            && array_key_exists($defaultMailer, $mailers)
            && $mailerTransports !== []
            && array_diff($mailerTransports, array_keys($mailers)) === []
            && array_intersect($mailerTransports, ['array', 'log', 'null']) === [];

        $checks = [
            'APP_ENV is production' => app()->environment('production'),
            'APP_DEBUG is disabled' => ! config('app.debug'),
            'APP_KEY is a supported encryption key' => filled($parsedAppKey) && Encrypter::supported($parsedAppKey, config('app.cipher')),
            'APP_URL uses HTTPS' => str_starts_with((string) config('app.url'), 'https://'),
            'FRONTEND_URL uses HTTPS' => str_starts_with((string) config('app.frontend_url'), 'https://'),
            'REVALIDATE_SECRET has at least 32 characters' => mb_strlen((string) config('app.revalidate_secret')) >= 32,
            'SEED_DEMO_CONTENT is disabled' => ! config('app.seed_demo_content'),
            'PostgreSQL is the active database' => ($database['driver'] ?? null) === 'pgsql',
            'Database password is configured' => filled($database['password'] ?? null),
            'Session storage is shared and persistent' => ! in_array(config('session.driver'), ['array', 'cookie'], true),
            'Session payload encryption is enabled' => config('session.encrypt') === true,
            'Session cookies require HTTPS' => config('session.secure') === true,
            'Queue processing is asynchronous' => ! in_array(config('queue.default'), ['sync', 'null'], true),
            'Application cache is persistent' => ! in_array(config('cache.default'), ['array', 'null'], true),
            'Administrator email is valid' => filter_var($adminEmail, FILTER_VALIDATE_EMAIL) !== false,
            'Administrator initial password has at least 12 characters' => mb_strlen($adminPassword) >= 12,
            'Lead notification email is valid' => filter_var($leadEmail, FILTER_VALIDATE_EMAIL) !== false,
            'Mail sender address is valid' => filter_var($mailFrom, FILTER_VALIDATE_EMAIL) !== false,
            'Outbound mailer does not fall back to logs' => $deliveryMailerConfigured,
            'SMTP credentials are configured when SMTP is used' => ! $usesSmtp || $smtpConfigured,
        ];

        $failed = 0;

        foreach ($checks as $label => $passed) {
            $this->line(sprintf('%s %s', $passed ? '[OK]' : '[FAIL]', $label));
            $failed += $passed ? 0 : 1;
        }

        if ($failed > 0) {
            $this->error("Production readiness check failed with {$failed} error(s).");

            return self::FAILURE;
        }

        $this->info('Production configuration is ready for deployment.');

        return self::SUCCESS;
    }
}
