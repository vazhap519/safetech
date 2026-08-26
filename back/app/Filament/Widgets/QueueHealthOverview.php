<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Throwable;

final class QueueHealthOverview extends BaseWidget
{
    protected ?string $heading = 'Queue / შეტყობინებების მდგომარეობა';

    protected ?string $description = 'ავტომატურად ახლდება ყოველ 15 წამში.';

    protected ?string $pollingInterval = '15s';

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $connection = (string) config('queue.default');

        if ($connection !== 'database') {
            return [
                Stat::make('Queue connection', $connection ?: 'unknown')
                    ->description('Database queue monitoring production-ზე აქტიურდება.'),
            ];
        }

        try {
            $queueDatabase = config('queue.connections.database.connection');
            $jobsTable = (string) config('queue.connections.database.table', 'jobs');
            $failedDatabase = config('queue.failed.database');
            $failedTable = (string) config('queue.failed.table', 'failed_jobs');

            $jobs = DB::connection($queueDatabase)->table($jobsTable);
            $pending = (int) $jobs->count();
            $oldestCreatedAt = $jobs->min('created_at');
            $oldestSeconds = $oldestCreatedAt === null
                ? null
                : max(0, now()->timestamp - (int) $oldestCreatedAt);
            $failed = (int) DB::connection($failedDatabase)->table($failedTable)->count();
            $staleSeconds = max(1, (int) config('queue.health_stale_seconds', 300));
            $delayed = $oldestSeconds !== null && $oldestSeconds > $staleSeconds;

            return [
                Stat::make('მომლოდინე Jobs', $pending)
                    ->description($delayed
                        ? 'Queue დაგვიანებულია — გადაამოწმეთ worker.'
                        : 'Queue ნორმალურად მუშავდება.')
                    ->color($delayed ? 'warning' : 'success'),
                Stat::make('Failed Jobs', $failed)
                    ->description($failed > 0
                        ? 'არის ჩავარდნილი job — საჭიროა მიზეზის შემოწმება.'
                        : 'ჩავარდნილი job არ არის.')
                    ->color($failed > 0 ? 'danger' : 'success'),
                Stat::make('ყველაზე ძველი Pending', $this->formatAge($oldestSeconds))
                    ->description($oldestSeconds === null
                        ? 'მომლოდინე job არ არის.'
                        : "ზღვარი: {$staleSeconds} წამი")
                    ->color($delayed ? 'warning' : 'success'),
            ];
        } catch (Throwable) {
            return [
                Stat::make('Queue monitoring', 'მიუწვდომელია')
                    ->description('Queue ცხრილების წაკითხვა ვერ მოხერხდა.')
                    ->color('danger'),
            ];
        }
    }

    private function formatAge(?int $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }

        if ($seconds < 60) {
            return $seconds.' წმ';
        }

        $minutes = intdiv($seconds, 60);

        if ($minutes < 60) {
            return $minutes.' წთ';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $remainingMinutes > 0
            ? $hours.' სთ '.$remainingMinutes.' წთ'
            : $hours.' სთ';
    }
}
