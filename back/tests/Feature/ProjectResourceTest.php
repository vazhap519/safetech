<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_cards_with_missing_icons_return_safe_defaults(): void
    {
        Project::query()->create([
            'slug' => 'null-icon-project',
            'name' => 'Null icon project',
            'title' => 'Null icon project',
            'description' => 'A published project with incomplete CMS card data.',
            'challenges' => [
                ['icon' => null, 'title' => 'Challenge', 'description' => 'Details'],
            ],
            'solutions' => [
                ['icon' => null, 'title' => 'Solution', 'description' => 'Details'],
            ],
            'is_published' => true,
        ]);

        $this->getJson('/api/projects/null-icon-project')
            ->assertOk()
            ->assertJsonPath('data.challenges.0.icon', 'security')
            ->assertJsonPath('data.solutions.0.icon', 'settings');
    }

    public function test_project_summary_supports_bounded_pagination(): void
    {
        foreach (range(1, 12) as $index) {
            Project::query()->create([
                'slug' => "paginated-project-{$index}",
                'name' => "Paginated project {$index}",
                'title' => "Paginated project {$index}",
                'description' => 'Published project summary.',
                'is_published' => true,
            ]);
        }

        $this->getJson('/api/projects?view=summary&page=2&per_page=9')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 9)
            ->assertJsonPath('meta.total', 12);
    }
}
