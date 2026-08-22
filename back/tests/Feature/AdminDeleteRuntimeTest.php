<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\CategoryForService;
use App\Models\Faq;
use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ReviewInvitation;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteRuntimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_service_category_can_be_deleted_without_deleting_its_services(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Runtime category',
            'slug' => 'runtime-category',
        ]);
        $service = Service::query()->create([
            'title' => 'Runtime service',
            'slug' => 'runtime-service',
            'short_description' => 'Runtime service description.',
            'category_for_service_id' => $category->id,
        ]);

        $this->assertTrue($category->delete());
        $this->assertDatabaseMissing('category_for_services', ['id' => $category->id]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'category_for_service_id' => null,
        ]);
    }

    public function test_a_service_can_be_deleted_without_foreign_key_failures(): void
    {
        $service = Service::query()->create([
            'title' => 'Delete-safe service',
            'slug' => 'delete-safe-service',
            'short_description' => 'Delete-safe service description.',
        ]);
        $faq = Faq::query()->create([
            'service_id' => $service->id,
            'context' => 'service',
            'question' => 'Can this service be deleted?',
            'answer' => 'Yes.',
            'is_active' => true,
        ]);
        $analytics = AnalyticsEvent::query()->create([
            'event_type' => AnalyticsEvent::TYPE_SERVICE_VIEW,
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'visitor_hash' => str_repeat('a', 64),
        ]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi',
            'location_name' => 'Tbilisi',
            'title' => 'Delete-safe landing',
            'content' => 'Delete-safe landing content.',
            'is_published' => false,
            'noindex' => true,
        ]);

        $this->assertTrue($service->delete());
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'service_id' => null,
        ]);
        $this->assertDatabaseHas('analytics_events', [
            'id' => $analytics->id,
            'service_id' => null,
        ]);
        $this->assertDatabaseMissing('local_service_landings', ['id' => $landing->id]);
    }

    public function test_a_project_category_can_be_deleted_without_deleting_its_projects(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Runtime projects',
            'slug' => 'runtime-projects',
        ]);
        $project = Project::query()->create([
            'name' => 'Runtime project',
            'title' => 'Runtime project',
            'slug' => 'runtime-project',
            'description' => 'Runtime project description.',
            'seo_description' => 'Runtime project SEO description.',
            'category_id' => $category->id,
            'is_published' => false,
        ]);

        $this->assertTrue($category->delete());
        $this->assertDatabaseMissing('project_categories', ['id' => $category->id]);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'category_id' => null,
        ]);
    }

    public function test_a_project_can_be_deleted_without_blocking_review_invitations(): void
    {
        $project = Project::query()->create([
            'name' => 'Delete-safe project',
            'title' => 'Delete-safe project',
            'slug' => 'delete-safe-project',
            'description' => 'Delete-safe project description.',
            'seo_description' => 'Delete-safe project SEO description.',
            'is_published' => false,
        ]);
        $invitation = ReviewInvitation::query()->create([
            'project_id' => $project->id,
            'recipient_name' => 'Runtime client',
            'is_active' => true,
        ]);

        $this->assertTrue($project->delete());
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('review_invitations', [
            'id' => $invitation->id,
            'project_id' => null,
        ]);
    }
}
