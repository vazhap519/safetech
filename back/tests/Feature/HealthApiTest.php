<?php

namespace Tests\Feature;

use App\Support\QueueWorkerHeartbeat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
                    'worker_status',
                    'worker_heartbeat_age_seconds',
                    'pending',
                    'failed',
                    'oldest_pending_seconds',
                ],
                'system' => [
                    'status',
                    'disk_free_bytes',
                    'disk_free_percent',
                    'memory_available_bytes',
                    'memory_available_percent',
                ],
            ]);

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('database', 'ok');
    }

    public function test_database_queue_health_marks_failed_jobs_as_degraded(): void
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
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('queue.connection', 'database')
            ->assertJsonPath('queue.status', 'ok')
            ->assertJsonPath('queue.pending', 1)
            ->assertJsonPath('queue.failed', 1);
    }

    public function test_database_queue_health_reports_a_missing_worker_heartbeat(): void
    {
        config()->set('queue.default', 'database');
        Cache::forget('health:queue-worker-heartbeat:database');
        QueueWorkerHeartbeat::resetThrottle();

        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('queue.status', 'ok')
            ->assertJsonPath('queue.worker_status', 'unknown')
            ->assertJsonPath('queue.worker_heartbeat_age_seconds', null);
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

    public function test_database_queue_health_detects_a_stale_worker_heartbeat(): void
    {
        config()->set('queue.default', 'database');
        config()->set('queue.health_heartbeat_stale_seconds', 60);
        QueueWorkerHeartbeat::resetThrottle();
        QueueWorkerHeartbeat::record();

        $this->travel(90)->seconds();

        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('queue.status', 'ok')
            ->assertJsonPath('queue.worker_status', 'stale')
            ->assertJsonPath('queue.worker_heartbeat_age_seconds', 90);
    }
}
