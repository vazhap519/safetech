<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_blog_uses_a_stable_array_contract(): void
    {
        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 1)
            ->assertJsonPath('meta.total', 0);

        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_it_returns_localized_cards_and_sanitized_post_content(): void
    {
        $category = Category::query()->create([
            'name' => 'ტექნოლოგია',
            'slug' => 'technology',
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'ტექნოლოგია',
                        'en' => 'Technology',
                        'ru' => 'Технологии',
                    ],
                ],
            ],
        ]);
        $author = Author::query()->create([
            'name' => 'ტექნიკური გუნდი',
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => 'ტექნიკური გუნდი',
                        'en' => 'Technical Team',
                        'ru' => 'Техническая команда',
                    ],
                ],
            ],
        ]);
        $post = Post::query()->create([
            'title' => 'ქსელის დაგეგმვა',
            'slug' => 'network-planning',
            'excerpt' => 'ქართული მოკლე აღწერა',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'reading_time' => 5,
            'meta_title' => 'ქსელის დაგეგმვის გზამკვლევი',
            'meta_description' => 'ქსელის დაგეგმვის პრაქტიკული გზამკვლევი ბიზნესისთვის.',
            'is_published' => true,
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'ქსელის დაგეგმვა',
                        'en' => 'Network Planning',
                        'ru' => 'Планирование сети',
                    ],
                    'excerpt' => [
                        'ka' => 'ქართული მოკლე აღწერა',
                        'en' => 'English article summary',
                        'ru' => 'Краткое описание статьи',
                    ],
                    'metaTitle' => [
                        'ka' => 'ქსელის დაგეგმვის გზამკვლევი',
                        'en' => 'Network Planning Guide',
                        'ru' => 'Руководство по планированию сети',
                    ],
                    'metaDescription' => [
                        'ka' => 'ქსელის დაგეგმვის პრაქტიკული გზამკვლევი ბიზნესისთვის.',
                        'en' => 'A practical network planning guide for businesses.',
                        'ru' => 'Практическое руководство по планированию сети для бизнеса.',
                    ],
                ],
            ],
        ]);
        $post->sections()->create([
            'title' => 'შეფასება',
            'content' => '<p>ქართული ტექსტი</p>',
            'position' => 1,
            'translations' => [
                'fields' => [
                    'title' => [
                        'ka' => 'შეფასება',
                        'en' => 'Assessment',
                        'ru' => 'Оценка',
                    ],
                    'content' => [
                        'ka' => '<p>ქართული ტექსტი</p>',
                        'en' => '<p>English body</p><script>alert(1)</script>',
                        'ru' => '<p>Русский текст</p>',
                    ],
                ],
            ],
        ]);

        $this->getJson('/api/blog?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Планирование сети')
            ->assertJsonPath('data.0.category.name', 'Технологии');

        $response = $this->getJson('/api/blog/network-planning?locale=en')
            ->assertOk()
            ->assertJsonPath('data.title', 'Network Planning')
            ->assertJsonPath('data.meta.title', 'Network Planning Guide')
            ->assertJsonPath('data.category.name', 'Technology')
            ->assertJsonPath('data.author.name', 'Technical Team')
            ->assertJsonPath('data.sections.0.title', 'Assessment')
            ->assertJsonPath('data.sections.0.content', '<p>English body</p>');

        $response->assertDontSee('<script', false);
    }

    public function test_it_does_not_expose_unpublished_posts(): void
    {
        $category = Category::query()->create([
            'name' => 'Private',
            'slug' => 'private',
        ]);
        Post::query()->create([
            'title' => 'Draft',
            'slug' => 'draft',
            'category_id' => $category->id,
            'is_published' => false,
        ]);

        $this->getJson('/api/blog/draft')->assertNotFound();
    }
}
