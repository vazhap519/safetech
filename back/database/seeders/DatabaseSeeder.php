<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ContentSeeder::class);
        $this->call(SeoPageSeeder::class);

        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            \App\Models\User::query()->updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                ['name' => env('ADMIN_NAME', 'SafeTech Admin'), 'password' => env('ADMIN_PASSWORD'), 'is_admin' => true],
            );
        }
    }
}
