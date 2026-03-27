<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::factory(30)->create()->each(function ($service) {

            $imageUrl = "https://picsum.photos/seed/" . rand(1,1000) . "/800/600";

            try {
                $service->addMediaFromUrl($imageUrl)
                    ->toMediaCollection('services');
            } catch (\Throwable $e) {
                logger($e->getMessage());
            }
        });
    }
}
