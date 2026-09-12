<?php

namespace Tests\Unit;

use App\Application\Ai\CmsContentGenerator;
use App\Filament\Concerns\HasAiContentGenerator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class CmsContentGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.openai.api_key', 'test-key');
        config()->set('services.openai.model', 'gpt-test');
    }

    public function test_local_seo_fills_every_empty_nested_translation_and_uses_structured_outputs(): void
    {
        Http::fake(function (Request $request) {
            $targets = data_get($request->data(), 'text.format.schema.properties.patches.items.properties.path.enum', []);
            $patches = collect($targets)->map(fn (string $path): array => [
                'path' => $path,
                'value_json' => json_encode("filled:{$path}", JSON_UNESCAPED_UNICODE),
            ])->all();

            return Http::response($this->responseWithPatches($patches));
        });

        $state = [
            'service_id' => 9,
            'location_name' => 'თბილისი',
            'location_slug' => 'tbilisi',
            'title' => '',
            'translations' => [
                'fields' => [
                    'title' => ['en' => null, 'ru' => ''],
                ],
            ],
            'benefits' => [[
                'title' => 'სწრაფი მომსახურება',
                'description' => 'ქართული აღწერა',
                'translations' => [
                    'en' => ['title' => null, 'description' => ''],
                    'ru' => ['title' => null, 'description' => ''],
                ],
            ]],
            'faq' => [[
                'question' => 'როგორ მუშაობს?',
                'answer' => 'ქართული პასუხი',
                'translations' => [
                    'en' => ['question' => null, 'answer' => ''],
                    'ru' => ['question' => null, 'answer' => ''],
                ],
            ]],
            'noindex' => true,
        ];

        $updates = app(CmsContentGenerator::class)->generate('local-seo', 'რეალური ფაქტები', $state);

        $this->assertSame('filled:title', data_get($updates, 'title'));
        $this->assertSame('filled:translations.fields.title.en', data_get($updates, 'translations.fields.title.en'));
        $this->assertSame('filled:translations.fields.title.ru', data_get($updates, 'translations.fields.title.ru'));
        $this->assertSame('filled:benefits.0.translations.en.title', data_get($updates, 'benefits.0.translations.en.title'));
        $this->assertSame('filled:benefits.0.translations.ru.description', data_get($updates, 'benefits.0.translations.ru.description'));
        $this->assertSame('filled:faq.0.translations.en.answer', data_get($updates, 'faq.0.translations.en.answer'));
        $this->assertSame('filled:faq.0.translations.ru.question', data_get($updates, 'faq.0.translations.ru.question'));
        $this->assertArrayNotHasKey('service_id', $updates);
        $this->assertArrayNotHasKey('location_slug', $updates);
        $this->assertArrayNotHasKey('noindex', $updates);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();
            $targets = data_get($data, 'text.format.schema.properties.patches.items.properties.path.enum', []);

            return $request->url() === 'https://api.openai.com/v1/responses'
                && data_get($data, 'text.format.type') === 'json_schema'
                && data_get($data, 'text.format.strict') === true
                && in_array('benefits.0.translations.en.title', $targets, true)
                && in_array('faq.0.translations.ru.answer', $targets, true)
                && ! in_array('location_slug', $targets, true)
                && ! in_array('noindex', $targets, true);
        });
    }

    public function test_it_retries_only_missing_fields_before_reporting_success(): void
    {
        Http::fakeSequence()
            ->push($this->responseWithPatches([
                ['path' => 'title', 'value_json' => json_encode('ქართული სათაური')],
            ]))
            ->push($this->responseWithPatches([
                ['path' => 'translations.fields.title.en', 'value_json' => json_encode('English title')],
                ['path' => 'translations.fields.title.ru', 'value_json' => json_encode('Русский заголовок')],
            ]));

        $updates = app(CmsContentGenerator::class)->generate('local-seo', 'რეალური ფაქტები', [
            'title' => '',
            'translations' => ['fields' => ['title' => ['en' => '', 'ru' => '']]],
        ]);

        $this->assertSame('ქართული სათაური', data_get($updates, 'title'));
        $this->assertSame('English title', data_get($updates, 'translations.fields.title.en'));
        $this->assertSame('Русский заголовок', data_get($updates, 'translations.fields.title.ru'));
        Http::assertSentCount(2);
    }

    public function test_it_rejects_a_response_that_remains_incomplete_after_retry(): void
    {
        Http::fakeSequence()
            ->push($this->responseWithPatches([
                ['path' => 'title', 'value_json' => json_encode('ქართული სათაური')],
            ]))
            ->push($this->responseWithPatches([
                ['path' => 'translations.fields.title.en', 'value_json' => json_encode('English title')],
            ]));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ყველა მოთხოვნილი ველი სრულად ვერ შეავსო');

        app(CmsContentGenerator::class)->generate('local-seo', 'რეალური ფაქტები', [
            'title' => '',
            'translations' => ['fields' => ['title' => ['en' => '', 'ru' => '']]],
        ]);
    }

    public function test_empty_local_seo_repeaters_require_all_three_languages(): void
    {
        Http::fakeSequence()
            ->push($this->responseWithPatches([
                [
                    'path' => 'benefits',
                    'value_json' => json_encode([[
                        'title' => 'ქართული',
                        'description' => 'აღწერა',
                    ]], JSON_UNESCAPED_UNICODE),
                ],
            ]))
            ->push($this->responseWithPatches([
                [
                    'path' => 'benefits',
                    'value_json' => json_encode([[
                        'title' => 'ქართული',
                        'description' => 'აღწერა',
                        'translations' => [
                            'en' => ['title' => 'English', 'description' => 'Description'],
                            'ru' => ['title' => 'Русский', 'description' => 'Описание'],
                        ],
                    ]], JSON_UNESCAPED_UNICODE),
                ],
            ]));

        $updates = app(CmsContentGenerator::class)->generate('local-seo', 'რეალური ფაქტები', [
            'benefits' => [],
        ]);

        $this->assertSame('English', data_get($updates, 'benefits.0.translations.en.title'));
        $this->assertSame('Описание', data_get($updates, 'benefits.0.translations.ru.description'));
        Http::assertSentCount(2);
    }

    public function test_profile_detection_uses_specific_resource_types_before_service_and_page_fallbacks(): void
    {
        $resolver = new AiProfileResolverStub;

        $this->assertSame('local-seo', $resolver->resolve('App\\Filament\\Resources\\LocalServiceLandingResource'));
        $this->assertSame('category', $resolver->resolve('App\\Filament\\Resources\\CategoryForServices\\CategoryForServiceResource'));
        $this->assertSame('category', $resolver->resolve('App\\Filament\\Resources\\ProjectCategories\\ProjectCategoryResource'));
        $this->assertSame('seo-page', $resolver->resolve('App\\Filament\\Resources\\SeoPages\\SeoPageResource'));
        $this->assertSame('team-member', $resolver->resolve('App\\Filament\\Resources\\TeamMemberResource'));
        $this->assertSame('testimonial', $resolver->resolve('App\\Filament\\Resources\\TestimonialResource'));
    }

    public function test_every_admin_ai_profile_targets_its_actual_multilingual_content_shape(): void
    {
        Http::fake(function (Request $request) {
            $targets = data_get($request->data(), 'text.format.schema.properties.patches.items.properties.path.enum', []);
            $patches = collect($targets)->map(fn (string $path): array => [
                'path' => $path,
                'value_json' => json_encode("filled:{$path}", JSON_UNESCAPED_UNICODE),
            ])->all();

            return Http::response($this->responseWithPatches($patches));
        });

        $cases = [
            ['project', ['translations' => ['fields' => ['title' => ['ka' => '']]]], 'translations.fields.title.ka'],
            ['service', ['translations' => ['fields' => ['title' => ['en' => '']]]], 'translations.fields.title.en'],
            ['page', ['translations' => ['fields' => ['content' => ['ru' => '']]]], 'translations.fields.content.ru'],
            ['about', ['about_page_translations' => ['about_hero_title' => ['ka' => '', 'en' => 'Ready', 'ru' => 'Готово']]], 'about_page_translations.about_hero_title.ka'],
            ['faq', ['translations' => ['fields' => ['answer' => ['en' => '']]]], 'translations.fields.answer.en'],
            ['category', ['translations' => ['fields' => ['seo_title' => ['ru' => '']]]], 'translations.fields.seo_title.ru'],
            ['seo-page', ['translations' => ['fields' => ['og_description' => ['ka' => '']]]], 'translations.fields.og_description.ka'],
            ['settings', ['key' => 'translations', 'value' => ['entries' => [['key' => 'footer.title', 'en' => '']]]], 'value.entries.0.en'],
            ['team-member', ['translations' => ['fields' => ['bio' => ['ru' => '']]]], 'translations.fields.bio.ru'],
            ['testimonial', ['translations' => ['fields' => ['quote' => ['en' => '']]]], 'translations.fields.quote.en'],
            ['partner', ['name' => 'Hikvision', 'category' => ''], 'category'],
        ];

        foreach ($cases as [$profile, $state, $expectedPath]) {
            $updates = app(CmsContentGenerator::class)->generate($profile, 'რეალური ფაქტები', $state);

            $this->assertSame("filled:{$expectedPath}", data_get($updates, $expectedPath), $profile);
        }
    }

    public function test_service_translation_routing_keys_are_never_rewritten(): void
    {
        Http::fake(function (Request $request) {
            $targets = data_get($request->data(), 'text.format.schema.properties.patches.items.properties.path.enum', []);
            $this->assertNotContains('translations.entries.0.key', $targets);
            $this->assertContains('translations.entries.0.en', $targets);
            $this->assertContains('translations.entries.0.ru', $targets);

            $patches = collect($targets)->map(fn (string $path): array => [
                'path' => $path,
                'value_json' => json_encode("filled:{$path}", JSON_UNESCAPED_UNICODE),
            ])->all();

            return Http::response($this->responseWithPatches($patches));
        });

        $updates = app(CmsContentGenerator::class)->generate('service', 'რეალური ფაქტები', [
            'translations' => ['entries' => [[
                'key' => 'benefit.0.title',
                'ka' => 'ქართული',
                'en' => '',
                'ru' => '',
            ]]],
        ], overwrite: true);

        $this->assertArrayNotHasKey('key', data_get($updates, 'translations.entries.0'));
        $this->assertSame('filled:translations.entries.0.en', data_get($updates, 'translations.entries.0.en'));
        $this->assertSame('filled:translations.entries.0.ru', data_get($updates, 'translations.entries.0.ru'));
    }

    public function test_incomplete_api_status_is_not_misreported_as_success(): void
    {
        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'status' => 'incomplete',
                'incomplete_details' => ['reason' => 'max_output_tokens'],
                'output' => [],
            ]),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('პასუხი არასრულია');

        app(CmsContentGenerator::class)->generate('page', 'რეალური ფაქტები', ['title' => '']);
    }

    public function test_site_settings_fill_managed_translations_without_touching_contact_or_integration_values(): void
    {
        Http::fake(function (Request $request) {
            $targets = data_get($request->data(), 'text.format.schema.properties.patches.items.properties.path.enum', []);
            $this->assertContains('managed_page_translations.contact_info_phone.en', $targets);
            $this->assertNotContains('value.phone', $targets);
            $this->assertNotContains('value.email', $targets);
            $this->assertNotContains('value.address', $targets);

            $patches = collect($targets)->map(fn (string $path): array => [
                'path' => $path,
                'value_json' => json_encode("filled:{$path}", JSON_UNESCAPED_UNICODE),
            ])->all();

            return Http::response($this->responseWithPatches($patches));
        });

        $updates = app(CmsContentGenerator::class)->generate('settings', 'რეალური ფაქტები', [
            'key' => 'contact',
            'value' => [
                'phone' => '',
                'email' => '',
                'address' => '',
                'whatsapp_message' => '',
            ],
            'managed_page_translations' => [
                'contact_info_phone' => ['ka' => 'ტელეფონი', 'en' => '', 'ru' => 'Телефон'],
            ],
        ]);

        $this->assertSame('filled:managed_page_translations.contact_info_phone.en', data_get($updates, 'managed_page_translations.contact_info_phone.en'));
        $this->assertSame('filled:value.whatsapp_message', data_get($updates, 'value.whatsapp_message'));
        $this->assertNull(data_get($updates, 'value.phone'));
        $this->assertNull(data_get($updates, 'value.email'));
        $this->assertNull(data_get($updates, 'value.address'));
    }

    public function test_service_generator_excludes_calculator_control_values_but_fills_labels(): void
    {
        Http::fake(function (Request $request) {
            $targets = data_get($request->data(), 'text.format.schema.properties.patches.items.properties.path.enum', []);
            $this->assertContains('lead_form.extra_fields.0.en', $targets);
            $this->assertNotContains('lead_form.extra_fields.0.type', $targets);
            $this->assertNotContains('lead_form.extra_fields.0.default', $targets);
            $this->assertNotContains('lead_form.extra_fields.0.options.0.value', $targets);
            $this->assertNotContains('lead_form.extra_fields.0.options.0.one_time_price', $targets);

            return Http::response($this->responseWithPatches([
                ['path' => 'lead_form.extra_fields.0.en', 'value_json' => json_encode('English label')],
                ['path' => 'lead_form.extra_fields.0.ru', 'value_json' => json_encode('Русская метка')],
            ]));
        });

        $updates = app(CmsContentGenerator::class)->generate('service', 'რეალური ფაქტები', [
            'lead_form' => ['extra_fields' => [[
                'key' => 'rooms',
                'type' => '',
                'default' => '',
                'ka' => 'ოთახები',
                'en' => '',
                'ru' => '',
                'options' => [[
                    'value' => '',
                    'ka' => 'ერთი',
                    'en' => 'One',
                    'ru' => 'Один',
                    'one_time_price' => '',
                ]],
            ]]],
        ]);

        $this->assertSame('English label', data_get($updates, 'lead_form.extra_fields.0.en'));
        $this->assertSame('Русская метка', data_get($updates, 'lead_form.extra_fields.0.ru'));
        $this->assertNull(data_get($updates, 'lead_form.extra_fields.0.type'));
        $this->assertNull(data_get($updates, 'lead_form.extra_fields.0.default'));
    }

    /** @param array<int, array{path: string, value_json: string}> $patches
     * @return array<string, mixed>
     */
    private function responseWithPatches(array $patches): array
    {
        return [
            'status' => 'completed',
            'output' => [[
                'type' => 'message',
                'content' => [[
                    'type' => 'output_text',
                    'text' => json_encode(['patches' => $patches], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]],
            ]],
        ];
    }
}

final class AiProfileResolverStub
{
    use HasAiContentGenerator;

    protected static string $resource = '';

    public function resolve(string $resource): string
    {
        self::$resource = $resource;

        return $this->aiContentProfile();
    }
}
