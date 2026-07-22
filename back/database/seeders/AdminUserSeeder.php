<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = trim((string) config('cms.admin.name', 'SafeTech Admin'));
        $email = mb_strtolower(trim((string) config('cms.admin.email')));
        $password = (string) config('cms.admin.password');

        if ($password === '' && ! app()->environment('production')) {
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('ADMIN_EMAIL must contain a valid email address.');
        }

        if (mb_strlen($password) < 12) {
            throw new RuntimeException('ADMIN_PASSWORD must contain at least 12 characters.');
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $attributes = [
            'name' => $name !== '' ? $name : 'SafeTech Admin',
            'is_admin' => true,
            'email_verified_at' => $admin->email_verified_at ?? now(),
        ];

        if (! $admin->exists) {
            $attributes['password'] = $password;
        }

        $admin->forceFill($attributes)->save();
    }
}
