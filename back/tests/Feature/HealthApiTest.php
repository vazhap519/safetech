<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class HealthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_confirms_application_and_database_readiness(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('database', 'ok')
            ->assertJsonPath('queue.connection', 'sync')
            ->assertJsonPath('queue.status', 'not_applicable')
            ->assertJsonStructure([
                'status',
                'commit',
                'database',
                'queue' => [
                    'connection',
                    'status',
                    'pending',
                    'failed',
                    'oldest_pending_seconds',
                ],
            ]);
    }

    public function test_database_queue_health_reports_pending_and_historical_failures(): void
    {
        config()->set('queue.default', 'database');

        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'Historical test failure',
            'failed_at' => now(),
        ]);

        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('queue.connection', 'database')
            ->assertJsonPath('queue.status', 'ok')
            ->assertJsonPath('queue.pending', 1)
            ->assertJsonPath('queue.failed', 1);
    }

    public function test_database_queue_health_marks_an_old_pending_job_as_delayed(): void
    {
        config()->set('queue.default', 'database');
        config()->set('queue.health_stale_seconds', 60);

        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->subMinutes(2)->timestamp,
            'created_at' => now()->subMinutes(2)->timestamp,
        ]);

        $response = $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('queue.status', 'delayed')
            ->assertJsonPath('queue.pending', 1)
            ->assertJsonPath('queue.failed', 0);

        $this->assertGreaterThanOrEqual(
            120,
            (int) $response->json('queue.oldest_pending_seconds'),
        );
    }
}
