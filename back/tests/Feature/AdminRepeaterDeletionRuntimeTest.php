<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\CategoryForService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRepeaterDeletionRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'repeater-admin@example.com');

        $admin = User::factory()->create([
            'email' => 'repeater-admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_service_repeater_items_can_be_removed_and_stay_removed_after_save(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Repeater service category',
            'slug' => 'repeater-service-category',
        ]);
        $service = Service::query()->create([
            'category_for_service_id' => $category->id,
            'name' => 'Repeater service',
            'slug' => 'repeater-service',
            'icon' => 'settings',
            'title' => 'Repeater service',
            'description' => 'Repeater service description.',
            'seo_description' => 'Repeater service SEO description.',
            'benefits' => [
                ['title' => 'Keep', 'description' => 'Keep this benefit.'],
                ['title' => 'Remove', 'description' => 'Remove this benefit.'],
            ],
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => [
                    'currency' => 'GEL',
                    'base_price' => 0,
                    'monthly_base_price' => 0,
                    'minimum_price' => 0,
                ],
                'project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა', 'en' => 'Small', 'ru' => 'Маленький'],
                    ['value' => 'large', 'ka' => 'დიდი', 'en' => 'Large', 'ru' => 'Большой'],
                ],
                'property_type_options' => [],
                'extra_fields' => [],
            ],
            'is_published' => false,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm([
                'benefits' => [
                    ['title' => 'Keep', 'description' => 'Keep this benefit.'],
                ],
                'lead_form.project_size_options' => [
                    ['value' => 'small', 'ka' => 'პატარა', 'en' => 'Small', 'ru' => 'Маленький'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();

        $this->assertSame(['Keep'], array_column($service->benefits ?? [], 'title'));
        $this->assertSame(
            ['small'],
            array_column(data_get($service->lead_form, 'project_size_options', []), 'value'),
        );
    }

    public function test_project_repeater_items_can_be_removed_and_stay_removed_after_save(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Repeater project category',
            'slug' => 'repeater-project-category',
        ]);
        $related = Project::query()->create([
            'category_id' => $category->id,
            'name' => 'Related project',
            'title' => 'Related project',
            'slug' => 'related-project-repeater-test',
            'description' => 'Related project description.',
            'seo_description' => 'Related project SEO description.',
            'icon' => 'business',
            'accent' => 'primary',
            'is_published' => false,
        ]);
        $project = Project::query()->create([
            'category_id' => $category->id,
            'name' => 'Repeater project',
            'title' => 'Repeater project',
            'slug' => 'repeater-project',
            'description' => 'Repeater project description.',
            'seo_description' => 'Repeater project SEO description.',
            'icon' => 'business',
            'accent' => 'primary',
            'meta' => [
                ['value' => 'Keep', 'label' => 'Kept row'],
                ['value' => 'Remove', 'label' => 'Removed row'],
            ],
            'related' => [
                ['slug' => $related->slug, 'title' => 'Related override'],
            ],
            'is_published' => false,
        ]);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->fillForm([
                'meta' => [
                    ['value' => 'Keep', 'label' => 'Kept row'],
                ],
                'related' => [],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $project->refresh();

        $this->assertSame(['Keep'], array_column($project->meta ?? [], 'value'));
        $this->assertSame([], $project->related ?? null);
    }
}
