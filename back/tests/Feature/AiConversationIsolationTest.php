<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiConversationIsolationTest extends TestCase
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

        Http::fake([
            'api.openai.com/*' => Http::response($this->textResponse()),
        ]);
    }

    public function test_a_conversation_id_cannot_be_resumed_from_a_different_ip(): void
    {
        $first = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->postJson('/api/ai/chat', [
                'message' => 'I need camera installation.',
                'locale' => 'en',
                'privacy' => true,
            ])
            ->assertOk();

        $firstConversationId = (string) $first->json('data.conversation_id');

        $second = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.11'])
            ->postJson('/api/ai/chat', [
                'conversation_id' => $firstConversationId,
                'message' => 'Continue the previous conversation.',
                'locale' => 'en',
                'privacy' => true,
            ])
            ->assertOk();

        $this->assertNotSame(
            $firstConversationId,
            (string) $second->json('data.conversation_id'),
        );
        $this->assertDatabaseCount('ai_conversations', 2);
    }

    public function test_feedback_can_only_be_submitted_from_the_conversation_ip(): void
    {
        $chat = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.20'])
            ->postJson('/api/ai/chat', [
                'message' => 'Which CCTV service do you recommend?',
                'locale' => 'en',
                'privacy' => true,
            ])
            ->assertOk();

        $messageId = (string) $chat->json('data.message_id');

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.21'])
            ->postJson("/api/ai/messages/{$messageId}/feedback", ['rating' => 1])
            ->assertNotFound();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.20'])
            ->postJson("/api/ai/messages/{$messageId}/feedback", ['rating' => 1])
            ->assertOk()
            ->assertJsonPath('data.saved', true);
    }

    /** @return array<string, mixed> */
    private function textResponse(): array
    {
        return [
            'id' => 'resp_test',
            'model' => 'gpt-5.6',
            'output' => [[
                'id' => 'msg_test',
                'type' => 'message',
                'role' => 'assistant',
                'content' => [[
                    'type' => 'output_text',
                    'text' => 'Test response',
                    'annotations' => [],
                ]],
            ]],
            'usage' => [
                'input_tokens' => 20,
                'output_tokens' => 10,
                'total_tokens' => 30,
            ],
        ];
    }
}
