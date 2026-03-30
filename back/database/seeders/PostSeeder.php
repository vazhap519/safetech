<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |------------------------------------------------------------------
        | 🔥 CATEGORIES
        |------------------------------------------------------------------
        */
        $categories = collect([
            'cctv' => 'CCTV',
            'network' => 'ქსელები',
            'pos' => 'POS სისტემები',
        ])->map(fn($name, $slug) =>
        Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        )
        )->values();

        /*
        |------------------------------------------------------------------
        | 🔥 AUTHOR
        |------------------------------------------------------------------
        */
        $author = Author::firstOrCreate(
            ['email' => 'admin@safetech.ge'],
            [
                'name' => 'Safetech Team',
                'slug' => 'safetech-team',
                'bio' => 'IT ექსპერტები',
            ]
        );

        /*
        |------------------------------------------------------------------
        | 🔥 POSTS (PRO VERSION)
        |------------------------------------------------------------------
        */
        Post::factory(30)
            ->state(function () use ($categories, $author) {
                return [
                    'category_id' => $categories->random()->id,
                    'author_id' => $author->id,
                ];
            })
            ->create()
            ->each(function ($post) {

                // 🖼 IMAGE
                try {
                    $post->clearMediaCollection('cover');

                    $post
                        ->addMediaFromUrl("https://picsum.photos/1200/630?random=" . rand(1, 1000))
                        ->toMediaCollection('cover');
                } catch (\Throwable $e) {
                    // ignore
                }

            });
    }
}
