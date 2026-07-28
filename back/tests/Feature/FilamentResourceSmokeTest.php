<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Models\Category;
use App\Models\CategoryForService;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFilter;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SeoPageSeeder;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentResourceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_renders_for_guests(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('wire:submit="authenticate"', false)
            ->assertSee('type="email"', false)
            ->assertSee('type="password"', false);
    }

    public function test_guests_are_redirected_to_login_from_protected_admin_pages(): void
    {
        foreach ([
            '/admin',
            '/admin/services',
            '/admin/projects',
            '/admin/site-settings',
            '/admin/seo-pages',
        ] as $url) {
            $response = $this->get($url)->assertStatus(302);

            $this->assertStringContainsString('/admin/login', (string) $response->headers->get('Location'));
        }
    }

    public function test_administrator_can_authenticate_through_the_filament_login_page(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $admin->email,
                'password' => 'password',
                'remember' => false,
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_administrators_cannot_authenticate_into_the_filament_admin_panel(): void
    {
        User::factory()->create([
            'email' => 'editor@example.com',
            'password' => 'password',
            'is_admin' => false,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'editor@example.com',
                'password' => 'password',
                'remember' => false,
            ])
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest();
    }

    public function test_key_admin_resource_list_create_and_edit_pages_render_for_an_administrator(): void
    {
        $this->seed(ContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        $blogCategory = Category::query()->create([
            'name' => 'Smoke Blog Category',
            'slug' => 'smoke-blog-category',
        ]);
        $post = Post::query()->create([
            'title' => 'Smoke Post',
            'slug' => 'smoke-post',
            'excerpt' => 'Smoke post excerpt',
            'body' => '<p>Smoke post body</p>',
            'category_id' => $blogCategory->getKey(),
            'is_published' => true,
        ]);
        $productCategory = ProductCategory::query()->create([
            'name' => 'Smoke Product Category',
            'slug' => 'smoke-product-category',
        ]);
        $productFilter = ProductFilter::query()->create([
            'name' => 'Brand',
            'slug' => 'brand',
            'options' => [
                ['label' => 'Dahua', 'slug' => 'dahua'],
            ],
        ]);
        $product = Product::query()->create([
            'product_category_id' => $productCategory->id,
            'name' => 'Smoke Product',
            'slug' => 'smoke-product',
            'short_description' => 'Short description',
            'description' => 'Long description',
            'filter_values' => [
                ['filter_slug' => 'brand', 'option_slugs' => ['dahua']],
            ],
            'is_published' => true,
        ]);

        $this->signInAdmin();

        $routes = [
            '/admin',
            '/admin/categories',
            '/admin/categories/create',
            "/admin/categories/{$blogCategory->getRouteKey()}/edit",
            '/admin/category-for-services',
            '/admin/category-for-services/create',
            '/admin/project-categories',
            '/admin/project-categories/create',
            '/admin/product-categories',
            '/admin/product-categories/create',
            '/admin/product-filters',
            '/admin/product-filters/create',
            '/admin/services',
            '/admin/services/create',
            '/admin/projects',
            '/admin/projects/create',
            '/admin/products',
            '/admin/products/create',
            '/admin/posts',
            '/admin/posts/create',
            "/admin/posts/{$post->getRouteKey()}/edit",
            '/admin/seo-pages',
            '/admin/seo-pages/create',
            '/admin/site-settings',
            '/admin/site-settings/create',
        ];

        $routes[] = '/admin/category-for-services/' . CategoryForService::query()->firstOrFail()->getRouteKey() . '/edit';
        $routes[] = '/admin/project-categories/' . ProjectCategory::query()->firstOrFail()->getRouteKey() . '/edit';
        $routes[] = "/admin/product-categories/{$productCategory->getRouteKey()}/edit";
        $routes[] = "/admin/product-filters/{$productFilter->getRouteKey()}/edit";
        $routes[] = '/admin/projects/' . Project::query()->firstOrFail()->getRouteKey() . '/edit';
        $routes[] = "/admin/products/{$product->getRouteKey()}/edit";
        $routes[] = '/admin/seo-pages/' . SeoPage::query()->where('key', 'contact')->firstOrFail()->getRouteKey() . '/edit';
        $routes[] = '/admin/site-settings/' . SiteSetting::query()->where('key', 'contact')->firstOrFail()->getRouteKey() . '/edit';

        foreach ($routes as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_blog_category_create_and_edit_actions_work_for_administrators(): void
    {
        $this->signInAdmin();

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => 'QA Smoke Category',
                'slug' => 'qa-smoke-category',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $category = Category::query()->where('slug', 'qa-smoke-category')->firstOrFail();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'name' => 'QA Smoke Category Updated',
                'slug' => 'qa-smoke-category-updated',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->getKey(),
            'name' => 'QA Smoke Category Updated',
            'slug' => 'qa-smoke-category-updated',
        ]);
    }

    private function signInAdmin(): User
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return $admin;
    }
}
