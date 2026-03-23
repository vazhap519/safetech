<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),

            'description' => $this->faker->paragraph(),

            'phone' => '+9955' . $this->faker->numberBetween(10000000, 99999999),
            'button_text' => 'დაგვიკავშირდი',

            'features' => [
                $this->faker->sentence(3),
                $this->faker->sentence(3),
                $this->faker->sentence(3),
            ],

            'faq' => [
                [
                    'q' => $this->faker->sentence(),
                    'a' => $this->faker->paragraph(),
                ],
            ],

            'seo_text' => [
                $this->faker->paragraph(),
            ],
        ];
    }
}
