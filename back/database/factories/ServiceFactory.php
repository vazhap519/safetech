<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),

            'description' => $this->faker->paragraphs(3, true),
            'short_description' => $this->faker->sentence(12),
            'long_description' => $this->faker->paragraphs(5, true),

            'phone' => '+9955' . $this->faker->numberBetween(10000000, 99999999),

            'button_text' => $this->faker->randomElement([
                'დაგვიკავშირდი',
                'უფასო კონსულტაცია',
                'დაგვირეკე ახლავე',
            ]),

            /*
            |------------------------------------------------------------------
            | 🔴 PROBLEMS (Pain Points)
            |------------------------------------------------------------------
            */
            'problems' => collect(range(1, rand(3, 5)))
                ->map(fn() => [
                    'text' => $this->faker->sentence(6),
                ])
                ->toArray(),

            /*
            |------------------------------------------------------------------
            | 🟢 FEATURES
            |------------------------------------------------------------------
            */
            /*
     |------------------------------------------------------------------
     | 🟢 FEATURES (FIXED)
     |------------------------------------------------------------------
     */
            'features' => collect(range(1, rand(3, 6)))
                ->map(fn() => [
                    'text' => $this->faker->sentence(5),
                ])
                ->toArray(),

            /*
            |------------------------------------------------------------------
            | 🟡 RESULTS (FIXED)
            |------------------------------------------------------------------
            */
            'results' => collect([
                '+'.$this->faker->numberBetween(20, 80).'% performance',
                '-'.$this->faker->numberBetween(20, 70).'% downtime',
                $this->faker->numberBetween(2, 5).'x faster systems',
            ])
                ->map(fn($item) => [
                    'text' => $item,
                ])
                ->toArray(),



            /*
            |------------------------------------------------------------------
            | 💼 CASE STUDY
            |------------------------------------------------------------------
            */
            'case_study' => [
                'title' => $this->faker->company(),
                'description' => $this->faker->sentence(12),
                'result' => '-'.$this->faker->numberBetween(40, 80).'% downtime',
            ],

            /*
            |------------------------------------------------------------------
            | ⭐ TESTIMONIALS
            |------------------------------------------------------------------
            */
            'testimonials' => collect(range(1, rand(2, 4)))
                ->map(fn() => [
                    'name' => $this->faker->name(),
                    'text' => $this->faker->sentence(12),
                ])
                ->toArray(),

            /*
            |------------------------------------------------------------------
            | ❓ FAQ
            |------------------------------------------------------------------
            */
            'faq' => collect(range(1, rand(2, 5)))
                ->map(fn() => [
                    'q' => $this->faker->sentence(),
                    'a' => $this->faker->paragraph(),
                ])
                ->toArray(),

            /*
            |------------------------------------------------------------------
            | 🎯 CTA
            |------------------------------------------------------------------
            */
            'cta_title' => $this->faker->randomElement([
                'დაგვიკავშირდი დღესვე',
                'მიიღე უფასო კონსულტაცია',
                'დაიწყე ახლა',
            ]),

            'cta_description' => $this->faker->sentence(10),

            /*
            |------------------------------------------------------------------
            | 🔥 SEO
            |------------------------------------------------------------------
            */
            'seo' => [
                'title' => $title . ' თბილისში',
                'description' => $this->faker->sentence(12),

                'keywords' => [
                    'IT სერვისები თბილისი',
                    'ქსელები',
                    'უსაფრთხოება',
                ],

                'content' => collect(range(1, 3))
                    ->map(fn() => $this->faker->paragraph())
                    ->toArray(),
            ],
        ];
    }
}
