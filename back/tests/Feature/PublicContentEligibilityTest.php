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

class PublicContentEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_published_services_and_projects_are_not_publicly_exposed(): void
    {
        $emptyServiceCategory = CategoryForService::query()->create([
            'name' => 'Empty services',
            'slug' => 'empty-services',
        ]);
        $serviceCategory = CategoryForService::query()->create([
            'name' => 'Security',
            'slug' => 'security',
        ]);

        Service::query()->create([
            'category_for_service_id' => $emptyServiceCategory->id,
            'slug' => 'empty-service',
            'name' => 'Empty service',
            'title' => 'Empty service',
            'description' => '   ',
            'short_description' => null,
            'long_description' => null,
            'is_published' => true,
        ]);
        Service::query()->create([
            'category_for_service_id' => $serviceCategory->id,
            'slug' => 'camera-installation',
            'name' => 'Camera installation',
            'title' => 'Camera installation',
            'description' => 'A complete camera installation service.',
            'short_description' => 'A complete camera installation service.',
            'is_published' => true,
        ]);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'camera-installation');
        $this->getJson('/api/services/empty-service')->assertNotFound();
        $this->getJson('/api/service-categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'security');

        $emptyProjectCategory = ProjectCategory::query()->create([
            'name' => 'Empty projects',
            'slug' => 'empty-projects',
        ]);
        $projectCategory = ProjectCategory::query()->create([
            'name' => 'Offices',
            'slug' => 'offices',
        ]);

        Project::query()->create([
            'category_id' => $emptyProjectCategory->id,
            'slug' => 'empty-project',
            'name' => 'Empty project',
            'title' => 'Empty project',
            'description' => '   ',
            'excerpt' => null,
            'content' => null,
            'is_published' => true,
        ]);
        Project::query()->create([
            'category_id' => $projectCategory->id,
            'slug' => 'office-network',
            'name' => 'Office network',
            'title' => 'Office network',
            'description' => 'A complete office network deployment.',
            'is_published' => true,
        ]);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'office-network');
        $this->getJson('/api/projects/empty-project')->assertNotFound();
        $this->getJson('/api/project-categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'offices');
    }

    public function test_visually_empty_blog_posts_are_excluded_from_lists_categories_and_details(): void
    {
        $emptyCategory = Category::query()->create([
            'name' => 'Empty category',
            'slug' => 'empty-category',
        ]);
        $category = Category::query()->create([
            'name' => 'Guides',
            'slug' => 'guides',
        ]);

        Post::query()->create([
            'title' => 'Empty post',
            'slug' => 'empty-post',
            'body' => '<p><br></p>',
            'category_id' => $emptyCategory->id,
            'is_published' => true,
        ]);
        Post::query()->create([
            'title' => 'Network guide',
            'slug' => 'network-guide',
            'body' => '<p>Useful network planning content.</p>',
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $this->getJson('/api/blog')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'network-guide')
            ->assertJsonPath('data.0.has_content', true)
            ->assertJsonPath('meta.total', 1);
        $this->getJson('/api/blog/empty-post')->assertNotFound();
        $this->getJson('/api/blog/network-guide')
            ->assertOk()
            ->assertJsonPath('data.sections.0.content', '<p>Useful network planning content.</p>');
        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'guides');
    }

    public function test_filtered_blog_results_keep_stable_pagination_across_cached_pages(): void
    {
        $category = Category::query()->create([
            'name' => 'Articles',
            'slug' => 'articles',
        ]);

        foreach (range(1, 10) as $number) {
            Post::query()->create([
                'title' => "Article {$number}",
                'slug' => "article-{$number}",
                'body' => "<p>Useful article {$number} content.</p>",
                'category_id' => $category->id,
                'is_published' => true,
            ]);
        }

        $this->getJson('/api/blog?page=1')
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 10);

        $this->getJson('/api/blog?page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 10);

        $this->getJson('/api/blog?page=-10')
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('meta.current_page', 1);
    }
}
