<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_published_dynamic_pages_with_localized_content(): void
    {
        Page::query()->create([
            'slug' => 'corporate-security',
            'title' => 'კორპორაციული უსაფრთხოება',
            'excerpt' => 'ქართული მოკლე აღწერა',
            'content' => 'ქართული გვერდის შინაარსი',
            'seo_title' => 'კორპორაციული უსაფრთხოება | SafeTech',
            'seo_description' => 'კორპორაციული უსაფრთხოების სრულყოფილი გადაწყვეტები.',
            'keywords' => ['უსაფრთხოება'],
            'translations' => [
                'fields' => [
                    'title' => ['en' => 'Corporate security'],
                    'excerpt' => ['en' => 'English summary'],
                    'content' => ['en' => 'English page content'],
                    'seoTitle' => ['en' => 'Corporate security | SafeTech'],
                    'seoDescription' => ['en' => 'Complete corporate security solutions.'],
                ],
            ],
            'is_published' => true,
        ]);
        Page::query()->create([
            'slug' => 'draft-page',
            'title' => 'Draft',
            'content' => 'Draft content',
            'is_published' => false,
        ]);

        $this->getJson('/api/pages?locale=en')
            ->assertOk()
            ->assertJsonFragment([
                'slug' => 'corporate-security',
                'title' => 'Corporate security',
            ])
            ->assertJsonMissing([
                'slug' => 'draft-page',
            ]);

        $this->getJson('/api/pages/corporate-security?locale=en')
            ->assertOk()
            ->assertJsonPath('data.content', 'English page content')
            ->assertJsonPath('data.seo.title', 'Corporate security | SafeTech');

        $this->getJson('/api/pages/draft-page')->assertNotFound();
    }

    public function test_noindex_pages_remain_public_but_are_marked_for_search_exclusion(): void
    {
        Page::query()->create([
            'slug' => 'private-offer',
            'title' => 'Private offer',
            'content' => 'Only direct visitors can see this page.',
            'is_published' => true,
            'noindex' => true,
        ]);

        $this->getJson('/api/pages/private-offer')
            ->assertOk()
            ->assertJsonPath('data.seo.noindex', true);
    }
}
