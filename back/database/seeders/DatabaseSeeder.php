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
        $this->call(SystemContentSeeder::class);

        if (! app()->environment('production') && config('app.seed_demo_content')) {
            $this->call(DemoContentSeeder::class);
        }

        $this->call(SeoPageSeeder::class);
        $this->call(AdminUserSeeder::class);
    }
}
