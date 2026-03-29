<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::updateOrCreate(
            ['email' => 'safetechcomge@gmail.com'], // უნიკალური ველი
            [
                'name' => 'vazha',
                'password' => Hash::make('SafeTech123!'),
                'is_admin' => true,
            ]
        );

        $this->call([
            ServiceSeeder::class,
            PostSeeder::class,
            AboutSeeder::class,
            ServiceSeeder::class,
            SeoPageSeeder::class,
            ContactPageSeeder::class,
            HomeSeeder::class,
SettingsSeeder::class,
        ]);
    }
}
