<?php

namespace Tests\Feature;

use App\Filament\Resources\CategoryForServices\Pages\EditCategoryForService;
use App\Filament\Resources\ProjectCategories\Pages\EditProjectCategory;
use App\Filament\Resources\ServiceConfiguratorResource\Pages\EditServiceConfigurator;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\CategoryForService;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRemainingRepeaterDeletionRuntimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cms.admin.email', 'remaining-repeater-admin@example.com');

        $admin = User::factory()->create([
            'email' => 'remaining-repeater-admin@example.com',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_service_category_faq_rows_can_be_deleted_in_all_locales(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'FAQ category',
            'slug' => 'faq-category',
            'faq' => [
                ['question' => 'Keep?', 'answer' => 'Keep.'],
                ['question' => 'Remove?', 'answer' => 'Remove.'],
            ],
            'translations' => [
                'faq' => [
                    'en' => [
                        ['question' => 'Keep EN?', 'answer' => 'Keep EN.'],
                        ['question' => 'Remove EN?', 'answer' => 'Remove EN.'],
                    ],
                ],
            ],
        ]);

        Livewire::test(EditCategoryForService::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'faq' => [
                    ['question' => 'Keep?', 'answer' => 'Keep.'],
                ],
                'translations.faq.en' => [
                    ['question' => 'Keep EN?', 'answer' => 'Keep EN.'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $category->refresh();
        $this->assertSame(['Keep?'], array_column($category->faq ?? [], 'question'));
        $this->assertSame(
            ['Keep EN?'],
            array_column(data_get($category->translations, 'faq.en', []), 'question'),
        );
    }

    public function test_project_category_faq_rows_can_be_deleted_and_stay_deleted(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Project FAQ category',
            'slug' => 'project-faq-category',
            'faq' => [
                ['question' => 'Keep project?', 'answer' => 'Keep.'],
                ['question' => 'Remove project?', 'answer' => 'Remove.'],
            ],
        ]);

        Livewire::test(EditProjectCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'faq' => [
                    ['question' => 'Keep project?', 'answer' => 'Keep.'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['Keep project?'],
            array_column($category->refresh()->faq ?? [], 'question'),
        );
    }

    public function test_service_advanced_translation_rows_can_be_deleted_and_stay_deleted(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Translation category',
            'slug' => 'translation-category',
        ]);
        $service = Service::query()->create([
            'category_for_service_id' => $category->id,
            'name' => 'Translation service',
            'slug' => 'translation-service',
            'icon' => 'settings',
            'title' => 'Translation service',
            'description' => 'Translation service description.',
            'seo_description' => 'Translation service SEO description.',
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => [
                    'currency' => 'GEL',
                    'base_price' => 0,
                    'monthly_base_price' => 0,
                    'minimum_price' => 0,
                ],
                'project_size_options' => [],
                'property_type_options' => [],
                'extra_fields' => [],
            ],
            'translations' => [
                'entries' => [
                    ['key' => 'legacy.keep', 'ka' => 'დატოვე', 'en' => 'Keep', 'ru' => 'Keep'],
                    ['key' => 'legacy.remove', 'ka' => 'წაშალე', 'en' => 'Remove', 'ru' => 'Remove'],
                ],
            ],
            'is_published' => false,
        ]);

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm([
                'translations.entries' => [
                    ['key' => 'legacy.keep', 'ka' => 'დატოვე', 'en' => 'Keep', 'ru' => 'Keep'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ['legacy.keep'],
            array_column(data_get($service->refresh()->translations, 'entries', []), 'key'),
        );
    }

    public function test_service_configurator_nested_rows_can_be_deleted_and_stay_deleted(): void
    {
        $category = CategoryForService::query()->create([
            'name' => 'Configurator category',
            'slug' => 'configurator-category',
        ]);
        $service = Service::query()->create([
            'category_for_service_id' => $category->id,
            'name' => 'Configurator service',
            'slug' => 'configurator-service',
            'icon' => 'settings',
            'title' => 'Configurator service',
            'description' => 'Configurator service description.',
            'seo_description' => 'Configurator service SEO description.',
            'lead_form' => [
                'calculator_enabled' => true,
                'pricing' => ['currency' => 'GEL'],
                'project_size_options' => [],
                'property_type_options' => [],
                'extra_fields' => [
                    [
                        'key' => 'technology',
                        'type' => 'select',
                        'ka' => 'ტექნოლოგია',
                        'options' => [
                            ['value' => 'ip', 'ka' => 'IP'],
                            ['value' => 'analog', 'ka' => 'Analog'],
                        ],
                    ],
                    ['key' => 'remove_field', 'type' => 'text', 'ka' => 'წასაშლელი'],
                ],
                'packages' => [
                    ['key' => 'keep', 'title_ka' => 'დატოვე'],
                    ['key' => 'remove', 'title_ka' => 'წაშალე'],
                ],
                'components' => [
                    [
                        'key' => 'keep-component',
                        'category' => 'other',
                        'title_ka' => 'დატოვე კომპონენტი',
                        'quantity_mode' => 'fixed',
                    ],
                    [
                        'key' => 'remove-component',
                        'category' => 'other',
                        'title_ka' => 'წაშალე კომპონენტი',
                        'quantity_mode' => 'fixed',
                    ],
                ],
            ],
            'is_published' => false,
        ]);

        Livewire::test(EditServiceConfigurator::class, ['record' => $service->getRouteKey()])
            ->fillForm([
                'lead_form.extra_fields' => [
                    [
                        'key' => 'technology',
                        'type' => 'select',
                        'ka' => 'ტექნოლოგია',
                        'options' => [
                            ['value' => 'ip', 'ka' => 'IP'],
                        ],
                    ],
                ],
                'lead_form.packages' => [
                    ['key' => 'keep', 'title_ka' => 'დატოვე'],
                ],
                'lead_form.components' => [
                    [
                        'key' => 'keep-component',
                        'category' => 'other',
                        'title_ka' => 'დატოვე კომპონენტი',
                        'quantity_mode' => 'fixed',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $leadForm = $service->refresh()->lead_form ?? [];
        $this->assertSame(['technology'], array_column(data_get($leadForm, 'extra_fields', []), 'key'));
        $this->assertSame(
            ['ip'],
            array_column(data_get($leadForm, 'extra_fields.0.options', []), 'value'),
        );
        $this->assertSame(['keep'], array_column(data_get($leadForm, 'packages', []), 'key'));
        $this->assertSame(
            ['keep-component'],
            array_column(data_get($leadForm, 'components', []), 'key'),
        );
    }
}
