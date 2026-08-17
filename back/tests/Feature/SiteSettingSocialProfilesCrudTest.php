<?php

namespace Tests\Feature;

use App\Filament\Resources\SiteSettingResource\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingSocialProfilesCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'admin@example.com');

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_administrator_can_add_a_social_profile_from_the_settings_form(): void
    {
        $setting = $this->socialsSetting([
            [
                'network' => 'facebook',
                'href' => 'https://facebook.com/safetech',
                'enabled' => true,
                'open_in_new_tab' => true,
            ],
        ]);

        Livewire::test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'key' => 'socials',
                'group' => 'general',
                'value' => [
                    'links_managed' => true,
                    'links' => [
                        [
                            'network' => 'facebook',
                            'href' => 'https://facebook.com/safetech',
                            'enabled' => true,
                            'open_in_new_tab' => true,
                        ],
                        [
                            'network' => 'instagram',
                            'href' => 'https://instagram.com/safetech',
                            'enabled' => true,
                            'open_in_new_tab' => true,
                        ],
                    ],
                    'share_enabled' => true,
                    'share_on_services' => true,
                    'share_on_projects' => true,
                    'share_title_ka' => 'გაზიარება',
                    'share_title_en' => 'Share',
                    'share_title_ru' => 'Поделиться',
                    'share_buttons' => [],
                ],
                'is_public' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $links = $setting->refresh()->value['links'] ?? [];

        $this->assertSame(['facebook', 'instagram'], array_column($links, 'network'));
        $this->assertSame(
            'https://instagram.com/safetech',
            collect($links)->firstWhere('network', 'instagram')['href'] ?? null,
        );
    }

    public function test_administrator_can_delete_one_or_all_social_profiles_from_the_settings_form(): void
    {
        $setting = $this->socialsSetting([
            [
                'network' => 'facebook',
                'href' => 'https://facebook.com/safetech',
                'enabled' => true,
                'open_in_new_tab' => true,
            ],
            [
                'network' => 'instagram',
                'href' => 'https://instagram.com/safetech',
                'enabled' => true,
                'open_in_new_tab' => true,
            ],
        ]);

        Livewire::test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'value' => [
                    'links_managed' => true,
                    'links' => [
                        [
                            'network' => 'instagram',
                            'href' => 'https://instagram.com/safetech',
                            'enabled' => true,
                            'open_in_new_tab' => true,
                        ],
                    ],
                    'share_enabled' => true,
                    'share_on_services' => true,
                    'share_on_projects' => true,
                    'share_title_ka' => 'გაზიარება',
                    'share_title_en' => 'Share',
                    'share_title_ru' => 'Поделиться',
                    'share_buttons' => [],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting->refresh();
        $this->assertSame(['instagram'], array_column($setting->value['links'] ?? [], 'network'));

        Livewire::test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm([
                'value' => [
                    'links_managed' => true,
                    'links' => [],
                    'share_enabled' => true,
                    'share_on_services' => true,
                    'share_on_projects' => true,
                    'share_title_ka' => 'გაზიარება',
                    'share_title_en' => 'Share',
                    'share_title_ru' => 'Поделиться',
                    'share_buttons' => [],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([], $setting->refresh()->value['links'] ?? null);
    }

    /** @param array<int, array<string, mixed>> $links */
    private function socialsSetting(array $links): SiteSetting
    {
        return SiteSetting::query()->create([
            'key' => 'socials',
            'group' => 'general',
            'value' => [
                'links_managed' => true,
                'links' => $links,
                'share_enabled' => true,
                'share_on_services' => true,
                'share_on_projects' => true,
                'share_title_ka' => 'გაზიარება',
                'share_title_en' => 'Share',
                'share_title_ru' => 'Поделиться',
                'share_buttons' => [],
            ],
            'is_public' => true,
        ]);
    }
}
