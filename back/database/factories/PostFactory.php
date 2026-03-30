<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        $description = $this->faker->sentence(12);

        return [
            'title' => $title,
            'slug' => Str::slug($this->faker->unique()->sentence(6)),
            'excerpt' => $this->faker->paragraph(2),
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',

            // ❗ აღარ ვწერთ აქ category_id-ს (Seeder მართავს)

            'reading_time' => rand(3, 10),
            'published_year' => now()->year,
            'is_published' => true,

            'seo' => [
                'title' => $title . ' თბილისში',
                'description' => $description,
                'keywords' => [
                    ['value' => $title],
                    ['value' => $title . ' თბილისი'],
                    ['value' => 'IT სერვისები'],
                ],
                'content' => [
                    ['text' => $description],
                    ['text' => "{$title} სერვისი თბილისში — ხარისხი და სანდოობა."],
                ],
            ],
        ];
    }
}
