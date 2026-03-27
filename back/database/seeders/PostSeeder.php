<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Author;
use App\Models\PostSection;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 🔥 CATEGORIES
        |--------------------------------------------------------------------------
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
        );

        /*
        |--------------------------------------------------------------------------
        | 🔥 AUTHOR
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | 🔥 POSTS
        |--------------------------------------------------------------------------
        */
        Post::factory(30)->make()->each(function ($post) use ($categories, $author) {

            $post->category_id = $categories->random()->id;
            $post->author_id = $author->id;

            /*
            |----------------------------------
            | 🔥 ENSURE SEO EXISTS
            |----------------------------------
            */
            if (!$post->seo) {
                $post->seo = [
                    'title' => $post->title . ' თბილისში',
                    'description' => $post->excerpt,
                    'keywords' => [
                        ['value' => $post->title],
                        ['value' => 'უსაფრთხოება'],
                    ],
                    'content' => [
                        ['text' => $post->excerpt],
                    ],
                ];
            }

            $post->save();

            /*
            |----------------------------------
            | 🖼 IMAGE
            |----------------------------------
            */
            $imageUrl = "https://picsum.photos/1200/630?random=" . rand(1, 1000);

            try {
                $post->clearMediaCollection('cover');

                $post
                    ->addMediaFromUrl($imageUrl)
                    ->toMediaCollection('cover');
            } catch (\Throwable $e) {
                // ignore
            }

            /*
            |----------------------------------
            | ✍️ SECTIONS
            |----------------------------------
            */
            PostSection::insert([
                [
                    'post_id' => $post->id,
                    'title' => 'შესავალი',
                    'content' => '<p>' . fake()->paragraph() . '</p>',
                    'position' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'post_id' => $post->id,
                    'title' => 'მთავარი ნაწილი',
                    'content' => '<p>' . fake()->paragraphs(2, true) . '</p>',
                    'position' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'post_id' => $post->id,
                    'title' => 'დასკვნა',
                    'content' => '<p>' . fake()->paragraph() . '</p>',
                    'position' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        });
    }
}
