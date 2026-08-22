<?php

namespace Tests\Feature;

use App\Models\AdminAudit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_audits_keep_identifiers_without_serializing_large_content_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $project = Project::query()->create([
            'name' => 'Audit delete project',
            'title' => 'Audit delete project',
            'slug' => 'audit-delete-project',
            'description' => str_repeat('Large project description. ', 500),
            'seo_description' => 'Audit delete SEO description.',
            'meta' => [[
                'label' => 'Large JSON payload',
                'value' => str_repeat('x', 12000),
            ]],
            'is_published' => false,
        ]);
        $projectId = $project->id;

        $this->assertTrue($project->delete());

        $audit = AdminAudit::query()
            ->where('action', 'deleted')
            ->where('auditable_type', Project::class)
            ->where('auditable_id', $projectId)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame($projectId, $audit->old_values['id'] ?? null);
        $this->assertSame('Audit delete project', $audit->old_values['name'] ?? null);
        $this->assertSame('audit-delete-project', $audit->old_values['slug'] ?? null);
        $this->assertArrayHasKey('_attribute_count', $audit->old_values);
        $this->assertArrayNotHasKey('description', $audit->old_values);
        $this->assertArrayNotHasKey('seo_description', $audit->old_values);
        $this->assertArrayNotHasKey('meta', $audit->old_values);
    }
}
