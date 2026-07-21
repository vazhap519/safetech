<?php

namespace Tests\Feature;

use App\Models\AdminAudit;
use App\Models\Service;
use App\Models\User;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_support_recoverable_app_authentication(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->assertInstanceOf(HasAppAuthentication::class, $user);
        $this->assertInstanceOf(HasAppAuthenticationRecovery::class, $user);
        $this->assertTrue(Schema::hasColumns('users', [
            'app_authentication_secret',
            'app_authentication_recovery_codes',
        ]));

        $user->saveAppAuthenticationSecret('test-mfa-secret');
        $user->saveAppAuthenticationRecoveryCodes(['recovery-code']);

        $raw = $user->fresh()->getAttributes();

        $this->assertNotSame('test-mfa-secret', $raw['app_authentication_secret']);
        $this->assertNotSame('["recovery-code"]', $raw['app_authentication_recovery_codes']);
        $this->assertArrayNotHasKey('app_authentication_secret', $user->fresh()->toArray());
        $this->assertArrayNotHasKey('app_authentication_recovery_codes', $user->fresh()->toArray());
    }

    public function test_admin_model_changes_are_audited_and_secrets_are_redacted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $service = Service::query()->create([
            'slug' => 'audited-service',
            'name' => 'Audited service',
            'title' => 'Audited service',
            'description' => 'Created by an administrator.',
            'is_published' => false,
        ]);

        $service->update(['title' => 'Updated audited service']);
        $admin->update(['password' => 'a-new-secure-password']);

        $this->assertDatabaseHas('admin_audits', [
            'action' => 'created',
            'auditable_type' => Service::class,
            'auditable_id' => $service->getKey(),
        ]);
        $this->assertDatabaseHas('admin_audits', [
            'action' => 'updated',
            'auditable_type' => Service::class,
            'auditable_id' => $service->getKey(),
        ]);

        $passwordAudit = AdminAudit::query()
            ->where('auditable_type', User::class)
            ->where('auditable_id', $admin->getKey())
            ->where('action', 'updated')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('[REDACTED]', $passwordAudit->old_values['password']);
        $this->assertSame('[REDACTED]', $passwordAudit->new_values['password']);
    }

    public function test_public_or_unauthenticated_changes_are_not_logged_as_admin_actions(): void
    {
        Service::query()->create([
            'slug' => 'system-service',
            'name' => 'System service',
            'title' => 'System service',
            'description' => 'Created outside an admin session.',
            'is_published' => false,
        ]);

        $this->assertSame(0, AdminAudit::query()->count());
    }
}
