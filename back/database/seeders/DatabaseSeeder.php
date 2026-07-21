<?php

namespace Database\Seeders;

use App\Models\User;
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
        $this->call(SystemContentSeeder::class);

        if (! app()->environment('production') && config('app.seed_demo_content')) {
            $this->call(DemoContentSeeder::class);
        }

        $this->call(SeoPageSeeder::class);

        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::query()->updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                ['name' => env('ADMIN_NAME', 'SafeTech Admin'), 'password' => env('ADMIN_PASSWORD'), 'is_admin' => true],
            );
        }
    }
}
