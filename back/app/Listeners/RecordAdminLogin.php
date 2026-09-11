<?php

namespace App\Listeners;

use App\Models\AdminAudit;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class RecordAdminLogin
{
    public function handle(Login $event): void
    {
        if (! $event->user->is_admin || ! Schema::hasTable('admin_audits')) {
            return;
        }

        try {
            AdminAudit::query()->create([
                'user_id' => $event->user->getAuthIdentifier(),
                'action' => 'login',
                'auditable_type' => $event->user::class,
                'auditable_id' => $event->user->getAuthIdentifier(),
                'label' => $event->user->email,
                'old_values' => [],
                'new_values' => ['guard' => $event->guard, 'remember' => $event->remember],
                'ip_address' => app()->bound('request') ? request()->ip() : null,
                'user_agent' => app()->bound('request')
                    ? mb_substr((string) request()->userAgent(), 0, 1000)
                    : null,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Unable to write the admin login audit.', [
                'user_id' => $event->user->getAuthIdentifier(),
                'exception' => $exception::class,
            ]);
        }
    }
}
