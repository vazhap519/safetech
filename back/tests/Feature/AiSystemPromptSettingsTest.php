<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSystemPromptSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.openai.enabled' => true,
            'services.openai.api_key' => 'test-key',
            'services.openai.model' => 'gpt-5.6',
        ]);
    }

    public function test_admin_system_prompt_is_used_and_kept_private(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'ai'],
            [
                'group' => 'general',
                'value' => [
                    'system_prompt' => 'CUSTOM SAFETECH PROMPT: ask for the object type before preparing a quote.',
                ],
                'is_public' => true,
            ],
        );

        Http::fake([
            'api.openai.com/*' => Http::response([
                'id' => 'resp_test',
                'model' => 'gpt-5.6',
                'output' => [[
                    'id' => 'msg_test',
                    'type' => 'message',
                    'role' => 'assistant',
                    'content' => [[
                        'type' => 'output_text',
                        'text' => 'როგორ დაგეხმაროთ?',
                        'annotations' => [],
                    ]],
                ]],
                'usage' => [
                    'input_tokens' => 20,
                    'output_tokens' => 10,
                    'total_tokens' => 30,
                ],
            ]),
        ]);

        $this->postJson('/api/ai/chat', [
            'message' => 'კამერების მონტაჟი მინდა.',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk();

        Http::assertSent(function ($request): bool {
            $instructions = (string) ($request['instructions'] ?? '');

            return str_contains($instructions, 'CUSTOM SAFETECH PROMPT')
                && str_contains($instructions, 'Mandatory runtime rules')
                && str_contains($instructions, 'Reply in Georgian');
        });

        $setting = SiteSetting::query()->where('key', 'ai')->firstOrFail();

        $this->assertFalse($setting->is_public);
        $this->assertFalse(SiteSetting::query()->public()->where('key', 'ai')->exists());
    }
}
