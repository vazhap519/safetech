<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryForService;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_categories_include_localized_admin_managed_seo(): void
    {
        $category = Category::query()->create($this->categoryAttributes('ბლოგი', 'blog-guides'));

        Post::query()->create([
            'category_id' => $category->id,
            'title' => 'Published post',
            'slug' => 'published-post',
            'excerpt' => 'Published article content.',
            'is_published' => true,
        ]);

        $this->getJson('/api/categories?locale=en')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'English category')
            ->assertJsonPath('data.0.seo_title', 'English SEO title')
            ->assertJsonPath('data.0.seo_keywords.0', 'network security')
            ->assertJsonPath('data.0.faq.0.question', 'English question')
            ->assertJsonPath('data.0.noindex', true);
    }

    public function test_service_and_project_categories_only_include_published_content(): void
    {
        $serviceCategory = CategoryForService::query()->create(
            $this->categoryAttributes('ქსელები', 'networking'),
        );
        $projectCategory = ProjectCategory::query()->create([
            ...$this->categoryAttributes('ოფისები', 'offices'),
            'sort_order' => 1,
        ]);

        Service::query()->create([
            'category_for_service_id' => $serviceCategory->id,
            'slug' => 'managed-networking',
            'name' => 'ქსელები',
            'title' => 'ქსელები',
            'description' => 'ქსელური ინფრასტრუქტურა',
            'seo_description' => 'ქსელური ინფრასტრუქტურა',
            'is_published' => true,
        ]);
        Project::query()->create([
            'category_id' => $projectCategory->id,
            'slug' => 'office-project',
            'name' => 'Office project',
            'title' => 'Office project',
            'description' => 'A complete office infrastructure project.',
            'is_published' => true,
        ]);

        $this->getJson('/api/service-categories?locale=en')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'English category');

        $this->getJson('/api/project-categories?locale=ru')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Русская категория');
    }

    public function test_editing_category_names_does_not_break_existing_public_urls(): void
    {
        $serviceCategory = CategoryForService::query()->create([
            'name' => 'Original service category',
            'slug' => 'stable-service-category',
        ]);
        $projectCategory = ProjectCategory::query()->create([
            'name' => 'Original project category',
            'slug' => 'stable-project-category',
        ]);

        $serviceCategory->update(['name' => 'Renamed service category']);
        $projectCategory->update(['name' => 'Renamed project category']);

        $this->assertSame('stable-service-category', $serviceCategory->fresh()->slug);
        $this->assertSame('stable-project-category', $projectCategory->fresh()->slug);
    }

    /** @return array<string, mixed> */
    private function categoryAttributes(string $name, string $slug): array
    {
        return [
            'name' => $name,
            'slug' => $slug,
            'seo_title' => 'ქართული SEO სათაური',
            'seo_description' => 'ქართული აღწერა',
            'seo_keywords' => [['value' => 'ქართული სიტყვა']],
            'intro_text' => '<p>ქართული შესავალი</p>',
            'faq' => [['question' => 'ქართული კითხვა', 'answer' => 'ქართული პასუხი']],
            'noindex' => true,
            'translations' => [
                'fields' => [
                    'name' => [
                        'ka' => $name,
                        'en' => 'English category',
                        'ru' => 'Русская категория',
                    ],
                    'seo_title' => [
                        'ka' => 'ქართული SEO სათაური',
                        'en' => 'English SEO title',
                        'ru' => 'Русский SEO заголовок',
                    ],
                    'seo_description' => [
                        'ka' => 'ქართული აღწერა',
                        'en' => 'English description',
                        'ru' => 'Русское описание',
                    ],
                    'intro_text' => [
                        'ka' => '<p>ქართული შესავალი</p>',
                        'en' => '<p>English introduction</p>',
                        'ru' => '<p>Русское вступление</p>',
                    ],
                ],
                'keywords' => [
                    'en' => ['network security'],
                    'ru' => ['безопасность сети'],
                ],
                'faq' => [
                    'en' => [['question' => 'English question', 'answer' => 'English answer']],
                    'ru' => [['question' => 'Русский вопрос', 'answer' => 'Русский ответ']],
                ],
            ],
        ];
    }
}
