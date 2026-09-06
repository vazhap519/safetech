<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_authenticated_admin_dashboard_renders_with_cache_clear_action(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('ქეშის გაწმენდა');
    }

    public function test_project_edit_page_exposes_direct_related_projects_manager(): void
    {
        $admin = $this->administrator();
        $category = ProjectCategory::query()->create([
            'name' => 'Projects',
            'slug' => 'projects',
        ]);
        $project = Project::query()->create([
            'name' => 'Current project',
            'title' => 'Current project',
            'slug' => 'current-project',
            'description' => 'Current project description.',
            'seo_description' => 'Current project SEO description.',
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get("/admin/projects/{$project->slug}/edit")
            ->assertOk()
            ->assertSee('Related Projects');
    }

    private function administrator(): User
    {
        config()->set('cms.admin.email', 'admin@example.com');

        return User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
    }
}
