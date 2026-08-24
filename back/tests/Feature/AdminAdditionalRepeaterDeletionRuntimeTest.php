<?php

namespace Tests\Feature;

use App\Filament\Resources\EstimateResource\Pages\EditEstimate;
use App\Filament\Resources\LocalServiceLandingResource\Pages\EditLocalServiceLanding;
use App\Filament\Resources\TeamMemberResource\Pages\EditTeamMember;
use App\Models\CategoryForService;
use App\Models\Estimate;
use App\Models\LocalServiceLanding;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAdditionalRepeaterDeletionRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'more-repeater-admin@example.com');

        $admin = User::factory()->create([
            'email' => 'more-repeater-admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_team_member_social_rows_can_be_removed_and_stay_removed(): void
    {
        $member = TeamMember::query()->create([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'position' => 'Technician',
            'socials' => [
                ['network' => 'facebook', 'href' => 'https://facebook.com/member'],
                ['network' => 'linkedin', 'href' => 'https://linkedin.com/in/member'],
            ],
            'is_active' => true,
        ]);

        Livewire::test(EditTeamMember::class, ['record' => $member->getRouteKey()])
            ->fillForm([
                'socials' => [
                    ['network' => 'linkedin', 'href' => 'https://linkedin.com/in/member'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['linkedin'],
            array_column($member->refresh()->socials ?? [], 'network'),
        );
    }

    public function test_local_landing_benefit_and_faq_rows_can_be_removed_and_stay_removed(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Local landing category',
            'slug' => 'local-landing-category',
        ]);
        $service = Service::query()->create([
            'category_for_service_id' => $category->id,
            'name' => 'Local landing service',
            'slug' => 'local-landing-service',
            'icon' => 'settings',
            'title' => 'Local landing service',
            'description' => 'Local landing service description.',
            'seo_description' => 'Local landing service SEO description.',
            'is_published' => false,
        ]);
        $landing = LocalServiceLanding::query()->create([
            'service_id' => $service->id,
            'location_name' => 'Tbilisi',
            'location_slug' => 'tbilisi-repeater-test',
            'title' => 'Tbilisi service page',
            'content' => 'Unique local service page content.',
            'benefits' => [
                ['title' => 'Keep', 'description' => 'Keep this benefit.'],
                ['title' => 'Remove', 'description' => 'Remove this benefit.'],
            ],
            'faq' => [
                ['question' => 'Keep question?', 'answer' => 'Keep answer.'],
                ['question' => 'Remove question?', 'answer' => 'Remove answer.'],
            ],
            'is_published' => false,
            'noindex' => true,
        ]);

        Livewire::test(EditLocalServiceLanding::class, ['record' => $landing->getRouteKey()])
            ->fillForm([
                'benefits' => [
                    ['title' => 'Keep', 'description' => 'Keep this benefit.'],
                ],
                'faq' => [
                    ['question' => 'Keep question?', 'answer' => 'Keep answer.'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $landing->refresh();
        $this->assertSame(['Keep'], array_column($landing->benefits ?? [], 'title'));
        $this->assertSame(['Keep question?'], array_column($landing->faq ?? [], 'question'));
    }

    public function test_estimate_manual_item_rows_can_be_removed_and_stay_removed(): void
    {
        $estimate = Estimate::query()->create([
            'project_type' => 'cctv',
            'camera_type' => 'ip',
            'camera_count' => 4,
            'camera_megapixels' => 4,
            'frames_per_second' => 15,
            'recording_hours_per_day' => 24,
            'recording_days' => 30,
            'markup_rate' => 0.6,
            'manual_items' => [
                ['label' => 'Keep item', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 10],
                ['label' => 'Remove item', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 20],
            ],
        ]);

        Livewire::test(EditEstimate::class, ['record' => $estimate->getRouteKey()])
            ->fillForm([
                'manual_items' => [
                    ['label' => 'Keep item', 'quantity' => 1, 'unit' => 'pcs', 'unit_cost' => 10],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['Keep item'],
            array_column($estimate->refresh()->manual_items ?? [], 'label'),
        );
    }
}
