<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AdminLoginAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_admin_login_is_audited(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Event::dispatch(new Login('web', $admin, false));

        $this->assertDatabaseHas('admin_audits', [
            'user_id' => $admin->getKey(),
            'action' => 'login',
            'auditable_type' => User::class,
            'auditable_id' => $admin->getKey(),
        ]);
    }

    public function test_non_admin_login_is_not_written_to_admin_audit(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Event::dispatch(new Login('web', $user, false));

        $this->assertDatabaseCount('admin_audits', 0);
    }
}
