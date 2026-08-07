<?php

namespace Tests\Feature;

use App\Filament\Resources\AboutPageResource\Pages\CreateAboutPageSetting;
use App\Filament\Resources\AboutPageResource\Pages\EditAboutPageSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AboutPageResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_initialize_missing_about_page_translations(): void
    {
        $this->authenticateAdministrator();

        SiteSetting::query()->where('key', 'translations')->delete();

        $this->assertDatabaseMissing('site_settings', ['key' => 'translations']);

        $this->get('/admin/about-pages')->assertOk();
        $this->get('/admin/about-pages/create')->assertOk();

        Livewire::test(CreateAboutPageSetting::class)
            ->fillForm([
                'about_page_translations' => [
                    'about_hero_title' => [
                        'ka' => 'ქართული სათაური',
                        'en' => 'English heading',
                        'ru' => 'Русский заголовок',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $setting = SiteSetting::query()->where('key', 'translations')->sole();

        $this->assertSame('general', $setting->group);
        $this->assertTrue($setting->is_public);
        $this->assertSame('ქართული სათაური', data_get($setting->value, 'entries.0.ka'));
        $this->assertSame('English heading', data_get($setting->value, 'entries.0.en'));
        $this->assertSame('Русский заголовок', data_get($setting->value, 'entries.0.ru'));
        $this->assertSame('about.hero.title', data_get($setting->value, 'entries.0.key'));
    }

    public function test_editing_one_about_section_preserves_other_translation_entries(): void
    {
        $this->authenticateAdministrator();

        $setting = SiteSetting::query()->where('key', 'translations')->sole();
        $setting->forceFill([
            'value' => [
                'entries' => [
                    [
                        'key' => 'about.hero.title',
                        'ka' => 'არსებული მთავარი სათაური',
                        'en' => 'Existing hero title',
                        'ru' => 'Существующий заголовок',
                    ],
                    [
                        'key' => 'about.who.title',
                        'ka' => 'ძველი ვინ ვართ',
                        'en' => 'Old who we are',
                        'ru' => 'Старое кто мы',
                    ],
                    [
                        'key' => 'nav.home',
                        'ka' => 'მთავარი',
                        'en' => 'Home',
                        'ru' => 'Главная',
                    ],
                ],
            ],
        ])->save();

        $this->get("/admin/about-pages/{$setting->id}/edit/identity")->assertOk();

        Livewire::test(EditAboutPageSetting::class, [
            'record' => $setting->getRouteKey(),
            'section' => 'identity',
        ])
            ->fillForm([
                'about_page_translations' => [
                    'about_who_title' => [
                        'ka' => 'განახლებული ვინ ვართ',
                        'en' => 'Updated who we are',
                        'ru' => 'Обновленное кто мы',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $entries = collect($setting->fresh()->value['entries'])->keyBy('key');

        $this->assertSame('Existing hero title', $entries['about.hero.title']['en']);
        $this->assertSame('Updated who we are', $entries['about.who.title']['en']);
        $this->assertSame('Home', $entries['nav.home']['en']);
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
