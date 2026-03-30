<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\CategoryForService;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 თუ კატეგორიები არ არის → შექმენი
        if (CategoryForService::count() === 0) {
            CategoryForService::insert([
                ['name' => 'Web Development', 'slug' => 'web-development'],
                ['name' => 'Cyber Security', 'slug' => 'cyber-security'],
                ['name' => 'Networking', 'slug' => 'networking'],
                ['name' => 'IT Support', 'slug' => 'it-support'],
            ]);
        }

        $categories = CategoryForService::pluck('id');

        Service::factory(30)->make()->each(function ($service) use ($categories) {

            // 🔗 category მიბმა
            $service->category_for_service_id = $categories->random();

            $service->save();

            // 🖼 image
            $imageUrl = "https://picsum.photos/seed/" . rand(1, 1000) . "/800/600";

            try {
                $service->addMediaFromUrl($imageUrl)
                    ->toMediaCollection('services'); // ⚠️ შენთან collection = services
            } catch (\Throwable $e) {
                logger($e->getMessage());
            }
        });
    }
}
