<?php

namespace Tests\Unit;

use App\Support\Observability\SlowQueryLogger;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class SlowQueryLoggerTest extends TestCase
{
    public function test_it_logs_only_the_sql_template_without_sensitive_bindings(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getName')->once()->andReturn('testing');

        $channel = Mockery::mock(LoggerInterface::class);
        $channel->shouldReceive('warning')
            ->once()
            ->with(
                'Slow database query detected.',
                Mockery::on(fn (array $context): bool => $context === [
                    'connection' => 'testing',
                    'duration_ms' => 125.68,
                    'sql_template' => 'select * from contact_leads where email = ?',
                ]),
            );

        $manager = Mockery::mock(LogManager::class);
        $manager->shouldReceive('channel')->once()->with('slow_queries')->andReturn($channel);
        Log::swap($manager);

        $logger = new SlowQueryLogger(100, 'slow_queries');
        $logger(new QueryExecuted(
            'select * from contact_leads where email = ?',
            ['private@example.com'],
            125.678,
            $connection,
        ));
    }

    public function test_it_ignores_queries_below_the_configured_threshold(): void
    {
        Log::shouldReceive('channel')->never();

        $connection = Mockery::mock(Connection::class);
        $connection->shouldReceive('getName')->once()->andReturn('testing');

        $logger = new SlowQueryLogger(100, 'slow_queries');
        $logger(new QueryExecuted('select 1', [], 99.99, $connection));
    }
}
