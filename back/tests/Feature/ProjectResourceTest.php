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
}
