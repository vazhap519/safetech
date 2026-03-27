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
            'short_description' => $this->faker->paragraphs(3, true),
            'long_description' => $this->faker->paragraphs(3, true),
            'phone' => '+9955' . $this->faker->numberBetween(10000000, 99999999),

            'button_text' => $this->faker->randomElement([
                'დაგვიკავშირდი',
                'დაგვირეკე ახლავე',
                'შეკვეთა',
            ]),

            /*
            |--------------------------------------------------------------------------
            | 🔥 FEATURES (dynamic)
            |--------------------------------------------------------------------------
            */
            'features' => collect(range(1, rand(3, 6)))
                ->map(fn() => $this->faker->sentence(4))
                ->toArray(),

            /*
            |--------------------------------------------------------------------------
            | ❓ FAQ (multiple Q&A)
            |--------------------------------------------------------------------------
            */
            'faq' => collect(range(1, rand(2, 5)))
                ->map(fn() => [
                    'q' => $this->faker->sentence(),
                    'a' => $this->faker->paragraph(),
                ])
                ->toArray(),

            /*
            |--------------------------------------------------------------------------
            | 🔥 SEO TEXT (long content)
            |--------------------------------------------------------------------------
            */
            'seo' => [
                'title' => $title . ' თბილისში',
                'description' => $this->faker->sentence(12),

                'keywords' => [
                    'IT სერვისები თბილისი',
                    'კამერების მონტაჟი',
                    'ქსელები',
                ],
                'content' => collect(range(1, 3))
                    ->map(fn() => $this->faker->paragraph())
                    ->toArray(),
                'faq' => collect(range(1, 3))
                    ->map(fn() => [
                        'q' => $this->faker->sentence(),
                        'a' => $this->faker->paragraph(),
                    ])
                    ->toArray(),
            ],
        ];
    }
}
