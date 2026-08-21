<?php

namespace Tests\Feature;

use App\Filament\Resources\TeamMemberResource\Pages\EditTeamMember;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamMemberSocialProfilesCrudTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_administrator_can_select_team_social_networks_and_save_their_links(): void
    {
        $this->authenticateAdministrator();

        $member = TeamMember::query()->create([
            'first_name' => 'Nino',
            'last_name' => 'Kiknadze',
            'position' => 'Engineer',
            'socials' => [
                'linkedin' => 'https://linkedin.com/in/nino',
                'email' => 'nino@example.com',
            ],
            'is_active' => true,
        ]);

        Livewire::test(EditTeamMember::class, ['record' => $member->getRouteKey()])
            ->assertFormSet([
                'socials' => [
                    ['network' => 'linkedin', 'href' => 'https://linkedin.com/in/nino'],
                    ['network' => 'email', 'href' => 'nino@example.com'],
                ],
            ])
            ->fillForm([
                'socials' => [
                    ['network' => 'linkedin', 'href' => 'https://linkedin.com/in/nino'],
                    ['network' => 'whatsapp', 'href' => '+995 599 123 456'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([
            'linkedin' => 'https://linkedin.com/in/nino',
            'whatsapp' => '+995 599 123 456',
        ], $member->refresh()->socials);
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
