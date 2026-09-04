<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Support\RelatedProjectDefaults;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RelatedProjectDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_related_project_populates_editable_card_values_for_all_three_locales(): void
    {
        $category = $this->category([
            'name' => 'Georgian category',
            'slug' => 'network-systems',
            'translations' => [
                'fields' => [
                    'name' => [
                        'en' => 'English category',
                        'ru' => 'Russian category',
                    ],
                ],
            ],
        ]);
        $related = $this->project([
            'slug' => 'related-network-project',
            'category_id' => $category->id,
            'title' => 'Georgian project title',
            'image_alt' => 'Georgian project image alt',
            'translations' => [
                'fields' => [
                    'title' => [
                        'en' => 'English project title',
                        'ru' => 'Russian project title',
                    ],
                    'imageAlt' => [
                        'en' => 'English project image alt',
                        'ru' => 'Russian project image alt',
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'title' => 'Georgian project title',
            'translations.en.title' => 'English project title',
            'translations.ru.title' => 'Russian project title',
            'category' => 'Georgian category',
            'translations.en.category' => 'English category',
            'translations.ru.category' => 'Russian category',
            'imageAlt' => 'Georgian project image alt',
            'translations.en.imageAlt' => 'English project image alt',
            'translations.ru.imageAlt' => 'Russian project image alt',
        ], RelatedProjectDefaults::forSlug($related->slug));
    }

    public function test_related_project_select_callback_updates_the_repeater_and_public_response_uses_selected_translations(): void
    {
        $this->authenticateAdministrator();

        $category = $this->category([
            'name' => 'Georgian category',
            'slug' => 'low-voltage',
            'translations' => [
                'fields' => [
                    'name' => [
                        'en' => 'English category',
                        'ru' => 'Russian category',
                    ],
                ],
            ],
        ]);
        $related = $this->project([
            'slug' => 'related-low-voltage-project',
            'category_id' => $category->id,
            'title' => 'Georgian project title',
            'image_alt' => 'Georgian project image alt',
            'translations' => [
                'fields' => [
                    'title' => [
                        'en' => 'English project title',
                        'ru' => 'Russian project title',
                    ],
                    'imageAlt' => [
                        'en' => 'English project image alt',
                        'ru' => 'Russian project image alt',
                    ],
                ],
            ],
        ]);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'name' => 'Current project',
                'title' => 'Current project',
                'slug' => 'current-project',
                'description' => 'Current project description.',
                'seo_description' => 'Current project SEO description.',
                'category_id' => $category->id,
                'icon' => 'business',
                'accent' => 'primary',
                'is_published' => true,
                'meta' => [],
                'scope' => [],
                'specs' => [],
                'challenges' => [],
                'solutions' => [],
                'process' => [],
                'results' => [],
                'related' => [
                    ['slug' => null],
                ],
            ])
            ->set('data.related.0.slug', $related->slug)
            ->assertFormSet([
                'related' => [[
                    'slug' => $related->slug,
                    'title' => 'Georgian project title',
                    'translations' => [
                        'en' => [
                            'title' => 'English project title',
                            'category' => 'English category',
                            'imageAlt' => 'English project image alt',
                        ],
                        'ru' => [
                            'title' => 'Russian project title',
                            'category' => 'Russian category',
                            'imageAlt' => 'Russian project image alt',
                        ],
                    ],
                    'category' => 'Georgian category',
                    'imageAlt' => 'Georgian project image alt',
                ]],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $current = Project::query()->where('slug', 'current-project')->sole();

        $this->getJson("/api/projects/{$current->slug}?locale=en")
            ->assertOk()
            ->assertJsonPath('data.related.0.title', 'English project title')
            ->assertJsonPath('data.related.0.category', 'English category')
            ->assertJsonPath('data.related.0.imageAlt', 'English project image alt');

        $this->getJson("/api/projects/{$current->slug}?locale=ru")
            ->assertOk()
            ->assertJsonPath('data.related.0.title', 'Russian project title')
            ->assertJsonPath('data.related.0.category', 'Russian category')
            ->assertJsonPath('data.related.0.imageAlt', 'Russian project image alt');
    }

    public function test_clearing_a_related_project_clears_its_generated_overrides(): void
    {
        $this->assertSame([
            'title' => null,
            'translations.en.title' => null,
            'translations.ru.title' => null,
            'category' => null,
            'translations.en.category' => null,
            'translations.ru.category' => null,
            'imageAlt' => null,
            'translations.en.imageAlt' => null,
            'translations.ru.imageAlt' => null,
        ], RelatedProjectDefaults::forSlug(null));

        $this->assertNull(RelatedProjectDefaults::forSlug('missing-project'));
    }

    /** @param array<string, mixed> $attributes */
    private function category(array $attributes): ProjectCategory
    {
        return ProjectCategory::query()->create($attributes);
    }

    /** @param array<string, mixed> $attributes */
    private function project(array $attributes): Project
    {
        return Project::query()->create([
            'name' => $attributes['name'] ?? $attributes['title'] ?? 'Project',
            'title' => $attributes['title'] ?? 'Project',
            'slug' => $attributes['slug'],
            'description' => $attributes['description'] ?? 'A complete project description.',
            'seo_description' => $attributes['seo_description'] ?? 'A complete project SEO description.',
            'is_published' => true,
            ...$attributes,
        ]);
    }

    private function authenticateAdministrator(): void
    {
        config()->set('cms.admin.email', 'admin@example.com');

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }
}
