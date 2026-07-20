<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentResourceSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_key_admin_resource_forms_render_for_an_administrator(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        foreach ([
            '/admin/categories/create',
            '/admin/category-for-services/create',
            '/admin/project-categories/create',
            '/admin/services/create',
            '/admin/projects/create',
            '/admin/posts/create',
            '/admin/partners/create',
            '/admin/team-members/create',
            '/admin/testimonials/create',
            '/admin/seo-pages/create',
            '/admin/site-settings/create',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
