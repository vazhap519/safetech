<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiLeadConversionResponseTest extends TestCase
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

    public function test_lead_created_is_true_only_on_the_turn_that_creates_the_lead(): void
    {
        Http::fakeSequence()
            ->push($this->toolCallResponse('create_lead', [
                'name' => 'ვაჟა',
                'phone' => '+995555123456',
                'service_slug' => null,
                'city' => 'თბილისი',
                'message' => 'მინდა კონსულტაცია.',
            ]))
            ->push($this->textResponse('მოთხოვნა მიღებულია.'))
            ->push($this->textResponse('გმადლობთ. კიდევ რამით დაგეხმაროთ?'));

        $first = $this->postJson('/api/ai/chat', [
            'message' => 'დამიკავშირდით +995 555 12 34 56 ნომერზე.',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk()
            ->assertJsonPath('data.lead_created', true)
            ->assertJsonPath('data.lead_exists', true);

        $this->postJson('/api/ai/chat', [
            'conversation_id' => $first->json('data.conversation_id'),
            'message' => 'მადლობა.',
            'locale' => 'ka',
            'privacy' => true,
        ])->assertOk()
            ->assertJsonPath('data.lead_created', false)
            ->assertJsonPath('data.lead_exists', true);
    }

    /** @return array<string, mixed> */
    private function textResponse(string $text): array
    {
        return [
            'id' => 'resp_text',
            'model' => 'gpt-5.6',
            'output' => [[
                'id' => 'msg_text',
                'type' => 'message',
                'role' => 'assistant',
                'content' => [[
                    'type' => 'output_text',
                    'text' => $text,
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

    /** @param array<string, mixed> $arguments
     * @return array<string, mixed>
     */
    private function toolCallResponse(string $name, array $arguments): array
    {
        return [
            'id' => 'resp_tool',
            'model' => 'gpt-5.6',
            'output' => [[
                'id' => 'fc_test',
                'type' => 'function_call',
                'call_id' => 'call_test',
                'name' => $name,
                'arguments' => json_encode($arguments, JSON_UNESCAPED_UNICODE),
                'status' => 'completed',
            ]],
            'usage' => [
                'input_tokens' => 20,
                'output_tokens' => 5,
                'total_tokens' => 25,
            ],
        ];
    }
}
