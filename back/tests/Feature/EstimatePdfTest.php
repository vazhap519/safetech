<?php

namespace Tests\Feature;

use App\Models\Estimate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstimatePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_a_private_unicode_pdf(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $estimate = Estimate::query()->create([
            'client_name' => 'ქართული კომპანია',
            'project_type' => 'cctv',
            'final_total' => 1250.50,
            'calculation' => [
                'line_items' => [[
                    'label' => 'ვიდეოკამერა',
                    'quantity' => 2,
                    'unit' => 'ცალი',
                    'sell_unit' => 625.25,
                    'sell_total' => 1250.50,
                ]],
            ],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.estimates.pdf', $estimate));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-content-type-options', 'nosniff')
            ->assertHeader('x-robots-tag', 'noindex, nofollow, noarchive');

        $cacheControl = (string) $response->headers->get('cache-control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringStartsWith('%PDF-', (string) $response->getContent());
    }

    public function test_non_admin_cannot_download_an_estimate_pdf(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $estimate = Estimate::query()->create(['final_total' => 0]);

        $this->actingAs($user)
            ->get(route('admin.estimates.pdf', $estimate))
            ->assertForbidden();
    }
}
