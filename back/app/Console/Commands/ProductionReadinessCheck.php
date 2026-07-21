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
        ];

        $failed = 0;

        foreach ($checks as $label => $passed) {
            $this->line(sprintf('%s %s', $passed ? '[OK]' : '[FAIL]', $label));
            $failed += $passed ? 0 : 1;
        }

        if (config('mail.default') === 'log') {
            $this->warn('[WARN] MAIL_MAILER is still set to log; lead notifications will not be delivered.');
        }

        if ($failed > 0) {
            $this->error("Production readiness check failed with {$failed} error(s).");

            return self::FAILURE;
        }

        $this->info('Production configuration is ready for deployment.');

        return self::SUCCESS;
    }
}
