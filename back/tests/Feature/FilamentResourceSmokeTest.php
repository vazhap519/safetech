<?php

namespace Tests\Feature;

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

    public function test_guests_are_redirected_to_login_from_core_admin_pages(): void
    {
        foreach ([
            '/admin',
            '/admin/services',
            '/admin/category-for-services',
            '/admin/projects',
            '/admin/project-categories',
            '/admin/site-settings',
            '/admin/seo-pages',
        ] as $url) {
            $response = $this->get($url)->assertStatus(302);

            $this->assertStringContainsString(
                '/admin/login',
                (string) $response->headers->get('Location'),
            );
        }
    }

    public function test_configured_administrator_can_authenticate_through_the_filament_login_page(): void
    {
        config()->set('cms.admin.email', 'admin@example.com');

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

    public function test_non_administrators_cannot_authenticate_into_the_admin_panel(): void
    {
        config()->set('cms.admin.email', 'editor@example.com');

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

    public function test_other_admin_flagged_users_cannot_authenticate(): void
    {
        config()->set('cms.admin.email', 'owner@example.com');

        User::factory()->create([
            'email' => 'other-admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'other-admin@example.com',
                'password' => 'password',
                'remember' => false,
            ])
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest();
    }

    public function test_core_admin_resource_pages_render_for_the_configured_administrator(): void
    {
        $this->seed(ContentSeeder::class);
        $this->seed(SeoPageSeeder::class);

        config()->set('cms.admin.email', 'admin@example.com');

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        foreach ([
            '/admin',
            '/admin/services',
            '/admin/category-for-services',
            '/admin/projects',
            '/admin/project-categories',
            '/admin/seo-pages',
            '/admin/site-settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/admin/users')->assertNotFound();
    }
}
