<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\DeploymentInfo;
use App\Support\QueueWorkerHeartbeat;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

final class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
        } catch (Throwable) {
            return response()->json([
                'status' => 'unavailable',
                'commit' => DeploymentInfo::commit(),
                'database' => 'unavailable',
                'queue' => $this->unavailableQueue(),
                'system' => $this->systemHealth(),
            ], 503);
        }

        $queue = $this->queueHealth();
        $system = $this->systemHealth();
        $degraded = in_array($queue['status'], ['delayed', 'unavailable'], true)
            || in_array($queue['worker_status'], ['unknown', 'stale', 'unavailable'], true)
            || (is_numeric($queue['failed']) && (int) $queue['failed'] > 0)
            || $system['status'] === 'degraded';

        return response()->json([
            'status' => $degraded ? 'degraded' : 'ok',
            'commit' => DeploymentInfo::commit(),
            'database' => 'ok',
            'queue' => $queue,
            'system' => $system,
        ]);
    }

    /** @return array<string, int|string|null> */
    private function queueHealth(): array
    {
        $connection = (string) config('queue.default');
        $health = [
            'connection' => $connection,
            'status' => 'not_applicable',
            'worker_status' => 'not_applicable',
            'worker_heartbeat_age_seconds' => null,
            'pending' => null,
            'failed' => null,
            'oldest_pending_seconds' => null,
        ];

        if ($connection !== 'database') {
            return $health;
        }

        try {
            $jobs = DB::connection(config('queue.connections.database.connection'))
                ->table((string) config('queue.connections.database.table', 'jobs'));
            $oldestCreatedAt = $jobs->min('created_at');
            $heartbeatAge = QueueWorkerHeartbeat::ageSeconds();
            $heartbeatStaleSeconds = max(
                10,
                (int) config('queue.health_heartbeat_stale_seconds', 120),
            );

            return [
                'connection' => $connection,
                'status' => $oldestCreatedAt !== null
                    && now()->timestamp - (int) $oldestCreatedAt > max(1, (int) config('queue.health_stale_seconds', 300))
                        ? 'delayed'
                        : 'ok',
                'worker_status' => match (true) {
                    $heartbeatAge === null => 'unknown',
                    $heartbeatAge > $heartbeatStaleSeconds => 'stale',
                    default => 'ok',
                },
                'worker_heartbeat_age_seconds' => $heartbeatAge,
                'pending' => (int) $jobs->count(),
                'failed' => (int) DB::connection(config('queue.failed.database'))
                    ->table((string) config('queue.failed.table', 'failed_jobs'))
                    ->count(),
                'oldest_pending_seconds' => $oldestCreatedAt === null
                    ? null
                    : max(0, now()->timestamp - (int) $oldestCreatedAt),
            ];
        } catch (Throwable) {
            return $this->unavailableQueue();
        }
    }

    /** @return array<string, int|string|null> */
    private function unavailableQueue(): array
    {
        return [
            'connection' => (string) config('queue.default'),
            'status' => 'unavailable',
            'worker_status' => 'unavailable',
            'worker_heartbeat_age_seconds' => null,
            'pending' => null,
            'failed' => null,
            'oldest_pending_seconds' => null,
        ];
    }

    /** @return array<string, float|int|string|null> */
    private function systemHealth(): array
    {
        $totalDisk = @disk_total_space(storage_path());
        $freeDisk = @disk_free_space(storage_path());
        $diskFreePercent = is_numeric($totalDisk) && $totalDisk > 0 && is_numeric($freeDisk)
            ? round(((float) $freeDisk / (float) $totalDisk) * 100, 1)
            : null;
        [$memoryTotal, $memoryAvailable] = $this->linuxMemory();
        $memoryAvailablePercent = $memoryTotal && $memoryAvailable !== null
            ? round(($memoryAvailable / $memoryTotal) * 100, 1)
            : null;
        $degraded = ($diskFreePercent !== null && $diskFreePercent < (float) config('health.disk_min_free_percent', 10))
            || ($memoryAvailablePercent !== null && $memoryAvailablePercent < (float) config('health.memory_min_available_percent', 5));

        return [
            'status' => $degraded ? 'degraded' : 'ok',
            'disk_free_bytes' => is_numeric($freeDisk) ? (int) $freeDisk : null,
            'disk_free_percent' => $diskFreePercent,
            'memory_available_bytes' => $memoryAvailable,
            'memory_available_percent' => $memoryAvailablePercent,
        ];
    }

    /** @return array{int|null, int|null} */
    private function linuxMemory(): array
    {
        if (PHP_OS_FAMILY !== 'Linux' || ! is_readable('/proc/meminfo')) {
            return [null, null];
        }

        $contents = @file_get_contents('/proc/meminfo');
        preg_match('/^MemTotal:\s+(\d+)\s+kB$/mi', (string) $contents, $total);
        preg_match('/^MemAvailable:\s+(\d+)\s+kB$/mi', (string) $contents, $available);

        return [
            isset($total[1]) ? (int) $total[1] * 1024 : null,
            isset($available[1]) ? (int) $available[1] * 1024 : null,
        ];
    }
}
