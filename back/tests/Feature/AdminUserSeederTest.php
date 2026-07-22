<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_provisions_the_configured_administrator_idempotently(): void
    {
        config()->set([
            'cms.admin.name' => 'SafeTech Admin',
            'cms.admin.email' => 'safetechgeorgia@gmail.com',
            'cms.admin.password' => 'initial-password-123',
        ]);

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'safetechgeorgia@gmail.com')->firstOrFail();

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue($admin->is_admin);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('initial-password-123', $admin->password));
    }

    public function test_it_does_not_reset_a_password_changed_in_filament(): void
    {
        User::factory()->create([
            'email' => 'safetechgeorgia@gmail.com',
            'password' => 'changed-admin-password',
            'is_admin' => false,
        ]);

        config()->set([
            'cms.admin.name' => 'SafeTech Admin',
            'cms.admin.email' => 'safetechgeorgia@gmail.com',
            'cms.admin.password' => 'initial-password-123',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'safetechgeorgia@gmail.com')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('changed-admin-password', $admin->password));
        $this->assertFalse(Hash::check('initial-password-123', $admin->password));
    }
}
