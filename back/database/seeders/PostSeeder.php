<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\Author;
use App\Models\PostSection;
use Illuminate\Support\Str;

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
                'bio' => 'IT და უსაფრთხოების ექსპერტები',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 🔥 POSTS
        |--------------------------------------------------------------------------
        */
        $posts = [
            ['title' => 'კამერების მონტაჟის სრული გზამკვლევი 2026', 'category' => 'cctv'],
            ['title' => 'როგორ ავირჩიოთ საუკეთესო უსაფრთხოების კამერა', 'category' => 'cctv'],
            ['title' => 'ინტერნეტის გაყვანის სრული გიდი', 'category' => 'network'],
            ['title' => 'როგორ დავაყენოთ WiFi ოფისში სწორად', 'category' => 'network'],
            ['title' => 'POS სისტემები რესტორნებისთვის', 'category' => 'pos'],
        ];

        foreach ($posts as $item) {

            $title = $item['title'];
            $slug = Str::slug($title);

            $category = $categories[$item['category']] ?? null;

            if (!$category) {
                continue; // 🔥 safe skip
            }

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'excerpt' => Str::limit(
                        'ეს არის სტატიის მოკლე აღწერა, რომელიც ეხმარება მომხმარებელს სწრაფად გაიგოს შინაარსი.',
                        120
                    ),
                    'body' => '<p>დეტალური კონტენტი აქ ჩაჯდება...</p>',
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'reading_time' => 5, // ✅ stable
                    'published_year' => now()->year,
                    'meta_title' => $title,
                    'meta_description' => Str::limit(
                        $title . ' - სრული გზამკვლევი Safetech-ისგან',
                        160
                    ),
                    'is_published' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 🔥 SECTIONS (IMPORTANT)
            |--------------------------------------------------------------------------
            */
            PostSection::where('post_id', $post->id)->delete();

            PostSection::insert([
                [
                    'post_id' => $post->id,
                    'title' => 'შესავალი',
                    'content' => '<p>ეს არის შესავალი ტექსტი...</p>',
                    'position' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'post_id' => $post->id,
                    'title' => 'მთავარი ნაწილი',
                    'content' => '<p>აქ მოდის მთავარი ინფორმაცია...</p>',
                    'position' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'post_id' => $post->id,
                    'title' => 'დასკვნა',
                    'content' => '<p>დასკვნითი ნაწილი...</p>',
                    'position' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
