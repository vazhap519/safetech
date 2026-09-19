<?php

namespace Tests\Feature;

use App\Application\Ai\CameraPlanVisionService;
use Illuminate\Http\Client\Request as OutboundRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CameraPlanVisionApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.openai.planner_enabled', true);
        config()->set('services.openai.planner_model', 'gpt-4.1-mini');
        config()->set('services.openai.api_key', 'vision-test-key');
    }

    private function payload(): array
    {
        return [
            'image' => UploadedFile::fake()->image('house.png', 900, 600),
            'width_meters' => '24',
            'scale_confirmed' => '0',
            'ai_consent' => '1',
            'locale' => 'ka',
        ];
    }

    private function draft(string $type = 'floor_plan', float $x = 250): array
    {
        return [
            'image_type' => $type,
            'confidence' => 'medium',
            'summary' => 'მონახაზი სახლისთვის',
            'caution' => 'შეამოწმეთ ადგილზე.',
            'walls' => [['ax' => 100, 'ay' => 100, 'bx' => 500, 'by' => 100]],
            'area' => [
                ['x' => 100, 'y' => 100],
                ['x' => 700, 'y' => 100],
                ['x' => 700, 'y' => 500],
                ['x' => 100, 'y' => 500],
            ],
            'rooms' => [['name' => 'მისაღები', 'polygon' => [['x' => 100, 'y' => 100], ['x' => 700, 'y' => 100], ['x' => 700, 'y' => 500]]]],
            'cameras' => [
                ['x' => $x, 'y' => 250, 'direction' => 90, 'kind' => 'bullet', 'reason' => 'შესასვლელი'],
            ],
            'notes' => ['აირჩიეთ რეალური ობიექტივი.'],
        ];
    }

    private function mockProvider(array $draft): void
    {
        Http::fake(['api.openai.com/v1/responses' => Http::response([
            'status' => 'completed',
            'output' => [[
                'type' => 'message',
                'content' => [['type' => 'output_text', 'text' => json_encode($draft, JSON_UNESCAPED_UNICODE)]],
            ]],
        ])]);
    }

    public function test_ai_requires_explicit_feature_flag_and_does_not_send_photo_when_off(): void
    {
        config()->set('services.openai.planner_enabled', false);
        Http::fake();

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertServiceUnavailable();

        Http::assertNothingSent();
    }

    public function test_ai_requires_separate_image_consent_before_any_paid_request(): void
    {
        Http::fake();
        $request = $this->payload();
        $request['ai_consent'] = '0';

        $this->post('/api/camera-plans/vision', $request, ['Accept' => 'application/json'])
            ->assertUnprocessable()->assertJsonValidationErrors('ai_consent');

        Http::assertNothingSent();
    }

    public function test_vision_draft_has_normalized_geometry_and_never_persists_image(): void
    {
        Storage::fake('local');
        $this->mockProvider($this->draft());

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.suggested_count', 1)
            ->assertJsonPath('data.cameras.0.x', 250)
            ->assertJsonPath('data.scale_confirmed', false)
            ->assertJsonCount(4, 'data.area')
            ->assertJsonPath('data.rooms.0.name', 'მისაღები');

        $this->assertSame([], Storage::disk('local')->allFiles('camera-plans'));
        Http::assertSent(fn (OutboundRequest $request): bool => $request->url() === 'https://api.openai.com/v1/responses'
            && $request['model'] === 'gpt-4.1-mini'
            && $request['store'] === false
            && str_starts_with((string) data_get($request->data(), 'input.0.content.1.image_url'), 'data:image/png;base64,')
            && data_get($request->data(), 'text.format.strict') === true);
    }

    public function test_photo_does_not_hallucinate_reliable_walls_or_floor_polygon(): void
    {
        $this->mockProvider($this->draft('photo'));

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.image_type', 'photo')
            ->assertJsonPath('data.walls', [])
            ->assertJsonPath('data.area', [])
            ->assertJsonPath('data.rooms', []);
    }

    public function test_provider_coordinates_outside_image_are_rejected_not_clamped_silently(): void
    {
        $this->mockProvider($this->draft('floor_plan', 5000));

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'AI plan needs manual review. Please try a clearer drawing.');
    }

    public function test_rate_limit_prevents_repeated_paid_inference(): void
    {
        $this->mockProvider($this->draft());

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertOk();
        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertStatus(429);

        Http::assertSentCount(1);
    }

    public function test_missing_key_returns_service_unavailable_without_provider_call(): void
    {
        config()->set('services.openai.api_key', '');
        Http::fake();

        $this->post('/api/camera-plans/vision', $this->payload(), ['Accept' => 'application/json'])
            ->assertServiceUnavailable();
        Http::assertNothingSent();
    }

    public function test_empty_cameras_are_allowed_when_image_cannot_support_placement(): void
    {
        $draft = $this->draft('unclear');
        $draft['cameras'] = [];
        $result = app(CameraPlanVisionService::class)->validateProposal($draft, false);

        $this->assertSame(0, $result['suggested_count']);
        $this->assertSame([], $result['walls']);
        $this->assertSame([], $result['area']);
        $this->assertSame([], $result['rooms']);
    }
}
