<?php

namespace Tests\Feature;

use App\Models\CameraPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CameraPlanApiTest extends TestCase
{
    use RefreshDatabase;

    private function plan(): array
    {
        return [
            'version' => 1,
            'widthMeters' => 20,
            'cameras' => [[
                'id' => 'cam-1', 'x' => 150, 'y' => 120,
                'direction' => 80, 'fov' => 90, 'range' => 18,
                'kind' => 'bullet', 'height' => 3, 'lens' => 2.8, 'sensor' => 5.6,
            ]],
            'walls' => [],
            'area' => [],
        ];
    }

    private function payload(): array
    {
        return [
            'title' => 'კოტეჯი',
            'contact_name' => 'Test Customer',
            'contact_phone' => '+995555123456',
            'contact_email' => 'customer@example.com',
            'privacy' => '1',
            'layout' => json_encode($this->plan(), JSON_THROW_ON_ERROR),
        ];
    }

    public function test_public_plan_stores_layout_with_no_walls_or_outline(): void
    {
        $this->postJson('/api/camera-plans', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.id', 1);

        $plan = CameraPlan::query()->firstOrFail();
        $this->assertCount(1, $plan->layout['cameras']);
        $this->assertSame([], $plan->layout['walls']);
        $this->assertSame([], $plan->layout['area']);
    }

    public function test_rejects_missing_consent_and_camera_geometry_outside_plan(): void
    {
        $data = $this->plan();
        $data['cameras'][0]['x'] = 2000;

        $this->postJson('/api/camera-plans', array_replace($this->payload(), [
            'privacy' => '0',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['privacy']);

        $this->postJson('/api/camera-plans', array_replace($this->payload(), [
            'layout' => json_encode($data, JSON_THROW_ON_ERROR),
        ]))->assertUnprocessable()->assertJsonValidationErrors(['cameras.0.x']);

        $this->assertDatabaseCount('camera_plans', 0);
    }

    public function test_saved_plan_is_only_readable_by_authorized_filament_admin(): void
    {
        config()->set('cms.admin.email', 'qa-admin@safetech.test');
        $this->postJson('/api/camera-plans', $this->payload())->assertCreated();
        $plan = CameraPlan::query()->firstOrFail();

        $regular = User::factory()->create([
            'email' => 'qa-other@safetech.test',
            'is_admin' => true,
        ]);
        $this->actingAs($regular)
            ->get(route('admin.camera-plans.layout', $plan))
            ->assertForbidden();

        $admin = User::factory()->create([
            'email' => 'qa-admin@safetech.test',
            'is_admin' => true,
        ]);
        $this->actingAs($admin)
            ->get(route('admin.camera-plans.layout', $plan))
            ->assertOk()
            ->assertJsonPath('cameras.0.kind', 'bullet')
            ->assertHeader('Cache-Control', 'private, no-store');
    }

    public function test_photo_is_private_and_deleted_with_plan(): void
    {
        Storage::fake('local');
        $data = $this->payload();
        $data['image'] = UploadedFile::fake()->image('floor.png', 900, 600);

        $this->post('/api/camera-plans', $data, ['Accept' => 'application/json'])
            ->assertCreated();

        $plan = CameraPlan::query()->firstOrFail();
        $this->assertNotNull($plan->background_path);
        Storage::disk('local')->assertExists($plan->background_path);
        $this->withHeaders(['Accept' => 'application/json'])
            ->get(route('admin.camera-plans.background', $plan))
            ->assertForbidden();

        $path = $plan->background_path;
        $plan->delete();
        Storage::disk('local')->assertMissing($path);
    }
}
