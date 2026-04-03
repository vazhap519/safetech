<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        /* =========================
           🔥 CATEGORIES
        ========================= */
        $categories = [
            'ვებ დეველოპმენტი',
            'მობილური აპები',
            'UI/UX დიზაინი',
            'E-commerce',
            'ინფრასტრუქტურა',
        ];

        $categoryMap = [];
        foreach ($categories as $name) {
            $cat = ProjectCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );

            $categoryMap[] = $cat->id;
        }

        /* =========================
           🔥 PROJECTS
        ========================= */
        for ($i = 1; $i <= 50; $i++) {

            $title = "პროექტი {$i}";

            $project = Project::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => "ეს არის პროექტი {$i}-ის მოკლე აღწერა. თანამედროვე ტექნოლოგიებით შექმნილი სისტემა.",
                'content' => "სრული აღწერა პროექტი {$i}-ზე. აქ შეგიძლია დაწერო დეტალური ინფორმაცია, გამოყენებული ტექნოლოგიები და შედეგები.",
                'category_id' => $categoryMap[array_rand($categoryMap)],
                'video_url' => null,
                'seo' => [
                    'title' => $title,
                    'description' => "SEO აღწერა {$title}-ისთვის",
                ],
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'sort_order' => rand(0, 10),
            ]);

            /* =========================
               🔥 FAKE IMAGE (Spatie)
            ========================= */
            $project
                ->addMediaFromUrl("https://picsum.photos/800/600?random={$i}")
                ->toMediaCollection('cover');

            /* =========================
               🔥 GALLERY (optional)
            ========================= */
            for ($g = 1; $g <= 3; $g++) {
                $project
                    ->addMediaFromUrl("https://picsum.photos/800/600?random={$i}{$g}")
                    ->toMediaCollection('gallery');
            }
        }
    }
}
