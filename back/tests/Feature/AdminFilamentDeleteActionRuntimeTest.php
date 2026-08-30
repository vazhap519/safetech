<?php

namespace Tests\Feature;

use App\Filament\Resources\CategoryForServices\Pages\EditCategoryForService;
use App\Filament\Resources\ContactLeadResource\Pages\EditContactLead;
use App\Filament\Resources\ProjectCategories\Pages\EditProjectCategory;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\AiConversation;
use App\Models\AnalyticsEvent;
use App\Models\CategoryForService;
use App\Models\ContactLead;
use App\Models\Faq;
use App\Models\LocalServiceLanding;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ReviewInvitation;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFilamentDeleteActionRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'delete-admin@example.com');

        $admin = User::factory()->create([
            'email' => 'delete-admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_service_category_delete_action_keeps_services_and_clears_the_relation(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Delete action category',
            'slug' => 'delete-action-category',
        ]);
        $service = Service::query()->create([
            'title' => 'Delete action service',
            'slug' => 'delete-action-service',
            'short_description' => 'Delete action service description.',
            'category_for_service_id' => $category->id,
        ]);

        Livewire::test(EditCategoryForService::class, ['record' => $category->getRouteKey()])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('category_for_services', ['id' => $category->id]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'category_for_service_id' => null,
        ]);
    }

    public function test_service_delete_action_handles_all_current_dependencies(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Service parent category',
            'slug' => 'service-parent-category',
        ]);
        $service = Service::query()->create([
            'title' => 'Dependent service',
            'slug' => 'dependent-service',
            'short_description' => 'Dependent service description.',
            'category_for_service_id' => $category->id,
        ]);
        $faq = Faq::query()->create([
            'service_id' => $service->id,
            'context' => 'service',
            'question' => 'Delete action dependency?',
            'answer' => 'Yes.',
            'is_active' => true,
        ]);
        $analytics = AnalyticsEvent::query()->create([
            'event_type' => AnalyticsEvent::TYPE_SERVICE_VIEW,
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'visitor_hash' => str_repeat('b', 64),
        ]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_slug' => 'tbilisi-delete-action',
            'location_name' => 'Tbilisi',
            'title' => 'Dependent landing',
            'content' => 'Dependent landing content.',
            'is_published' => false,
            'noindex' => true,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'service_id' => null]);
        $this->assertDatabaseHas('analytics_events', ['id' => $analytics->id, 'service_id' => null]);
        $this->assertDatabaseMissing('local_service_landings', ['id' => $landing->id]);
    }

    public function test_project_category_delete_action_keeps_projects_and_clears_the_relation(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Delete action projects',
            'slug' => 'delete-action-projects',
        ]);
        $project = Project::query()->create([
            'name' => 'Delete action project',
            'title' => 'Delete action project',
            'slug' => 'delete-action-project',
            'description' => 'Delete action project description.',
            'seo_description' => 'Delete action project SEO description.',
            'category_id' => $category->id,
            'is_published' => false,
        ]);

        Livewire::test(EditProjectCategory::class, ['record' => $category->getRouteKey()])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('project_categories', ['id' => $category->id]);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'category_id' => null,
        ]);
    }

    public function test_project_delete_action_does_not_fail_when_review_invitations_exist(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Project parent category',
            'slug' => 'project-parent-category',
        ]);
        $project = Project::query()->create([
            'name' => 'Dependent project',
            'title' => 'Dependent project',
            'slug' => 'dependent-project',
            'description' => 'Dependent project description.',
            'seo_description' => 'Dependent project SEO description.',
            'category_id' => $category->id,
            'is_published' => false,
        ]);
        $invitation = ReviewInvitation::query()->create([
            'project_id' => $project->id,
            'recipient_name' => 'Delete action client',
            'is_active' => true,
        ]);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('review_invitations', [
            'id' => $invitation->id,
            'project_id' => null,
        ]);
    }

    public function test_contact_lead_delete_action_clears_ai_relation_and_removes_the_lead(): void
    {
        $lead = ContactLead::query()->create([
            'name' => 'Deletion request',
            'phone' => '+995555123456',
            'source' => 'contact-page',
        ]);
        $conversation = AiConversation::query()->create([
            'contact_lead_id' => $lead->id,
            'locale' => 'ka',
        ]);

        Livewire::test(EditContactLead::class, ['record' => $lead->getRouteKey()])
            ->assertActionExists('delete')
            ->callAction('delete');

        $this->assertDatabaseMissing('contact_leads', ['id' => $lead->id]);
        $this->assertDatabaseHas('ai_conversations', [
            'id' => $conversation->id,
            'contact_lead_id' => null,
        ]);
    }
}
