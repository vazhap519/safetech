<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantToggleTest extends TestCase
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

    public function test_admin_toggle_hides_ai_assistant_and_disables_chat_api(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'integrations'],
            [
                'group' => 'general',
                'value' => ['ai_assistant_enabled' => false],
                'is_public' => true,
            ],
        );

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('ai_assistant.enabled', false);

        $this->postJson('/api/ai/chat', [
            'message' => 'Hello',
            'locale' => 'en',
            'privacy' => true,
        ])->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_ai_assistant_remains_available_when_admin_toggle_is_enabled(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'integrations'],
            [
                'group' => 'general',
                'value' => ['ai_assistant_enabled' => true],
                'is_public' => true,
            ],
        );

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('ai_assistant.enabled', true);
    }
}
