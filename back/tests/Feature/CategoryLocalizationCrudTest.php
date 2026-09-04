<?php

namespace Tests\Feature;

use App\Filament\Resources\CategoryForServices\Pages\CreateCategoryForService;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Models\CategoryForService;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryLocalizationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_must_provide_english_and_russian_names_when_creating_categories(): void
    {
        $this->authenticateAdministrator();

        Livewire::test(CreateCategoryForService::class)
            ->fillForm([
                'name' => 'უსაფრთხოების სისტემები',
                'slug' => 'security-systems',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'translations.fields.name.en' => 'required',
                'translations.fields.name.ru' => 'required',
            ]);

        Livewire::test(CreateCategoryForService::class)
            ->fillForm($this->categoryFormData(
                'უსაფრთხოების სისტემები',
                'Security systems',
                'Системы безопасности',
                'security-systems',
            ))
            ->call('create')
            ->assertHasNoFormErrors();

        Livewire::test(CreateProjectCategory::class)
            ->fillForm($this->categoryFormData(
                'ოფისები',
                'Offices',
                'Офисы',
                'offices',
            ))
            ->call('create')
            ->assertHasNoFormErrors();

        $serviceCategory = CategoryForService::query()->where('slug', 'security-systems')->sole();
        $projectCategory = ProjectCategory::query()->where('slug', 'offices')->sole();

        $this->assertSame('უსაფრთხოების სისტემები', $serviceCategory->name);
        $this->assertSame('უსაფრთხოების სისტემები', data_get($serviceCategory->translations, 'fields.name.ka'));
        $this->assertSame('Security systems', data_get($serviceCategory->translations, 'fields.name.en'));
        $this->assertSame('Системы безопасности', data_get($serviceCategory->translations, 'fields.name.ru'));
        $this->assertSame('ოფისები', data_get($projectCategory->translations, 'fields.name.ka'));
        $this->assertSame('Offices', data_get($projectCategory->translations, 'fields.name.en'));
        $this->assertSame('Офисы', data_get($projectCategory->translations, 'fields.name.ru'));
    }

    public function test_public_category_apis_return_the_name_and_seo_for_the_requested_locale(): void
    {
        $serviceCategory = CategoryForService::query()->create([
            'name' => 'უსაფრთხოება',
            'slug' => 'security',
            'seo_title' => 'უსაფრთხოების სისტემები',
            'seo_description' => 'უსაფრთხოების სისტემები თბილისში.',
            'translations' => $this->translations(
                'უსაფრთხოება',
                'Security',
                'Безопасность',
                'უსაფრთხოების სისტემები',
                'Security systems',
                'Системы безопасности',
            ),
        ]);

        Service::query()->create([
            'category_for_service_id' => $serviceCategory->id,
            'slug' => 'camera-installation',
            'name' => 'კამერების მონტაჟი',
            'title' => 'კამერების მონტაჟი',
            'description' => 'სრული კამერების მონტაჟის მომსახურება.',
            'is_published' => true,
        ]);

        $projectCategory = ProjectCategory::query()->create([
            'name' => 'ოფისები',
            'slug' => 'offices',
            'seo_title' => 'ოფისის ინფრასტრუქტურა',
            'seo_description' => 'ოფისის ტექნოლოგიური პროექტები.',
            'translations' => $this->translations(
                'ოფისები',
                'Offices',
                'Офисы',
                'ოფისის ინფრასტრუქტურა',
                'Office infrastructure',
                'Офисная инфраструктура',
            ),
        ]);

        Project::query()->create([
            'category_id' => $projectCategory->id,
            'slug' => 'office-network',
            'name' => 'ოფისის ქსელი',
            'title' => 'ოფისის ქსელი',
            'description' => 'ოფისის ქსელური ინფრასტრუქტურის პროექტი.',
            'is_published' => true,
        ]);

        $this->getJson('/api/service-categories?locale=en')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Security')
            ->assertJsonPath('data.0.seo_title', 'Security systems');

        $this->getJson('/api/service-categories?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Безопасность')
            ->assertJsonPath('data.0.seo_title', 'Системы безопасности');

        $this->getJson('/api/project-categories?locale=en')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Offices')
            ->assertJsonPath('data.0.seo_title', 'Office infrastructure');

        $this->getJson('/api/project-categories?locale=ru')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Офисы')
            ->assertJsonPath('data.0.seo_title', 'Офисная инфраструктура');
    }

    /** @return array<string, mixed> */
    private function categoryFormData(string $ka, string $en, string $ru, string $slug): array
    {
        return [
            'name' => $ka,
            'slug' => $slug,
            'translations' => [
                'fields' => [
                    'name' => [
                        'en' => $en,
                        'ru' => $ru,
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function translations(
        string $nameKa,
        string $nameEn,
        string $nameRu,
        string $seoTitleKa,
        string $seoTitleEn,
        string $seoTitleRu,
    ): array {
        return [
            'fields' => [
                'name' => [
                    'ka' => $nameKa,
                    'en' => $nameEn,
                    'ru' => $nameRu,
                ],
                'seo_title' => [
                    'ka' => $seoTitleKa,
                    'en' => $seoTitleEn,
                    'ru' => $seoTitleRu,
                ],
            ],
        ];
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
