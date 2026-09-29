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

    public function test_quote_engine_pdf_uses_client_note_and_never_internal_notes(): void
    {
        $estimate = Estimate::query()->create([
            'client_name' => 'კლიენტი',
            'project_type' => 'service',
            'project_title' => 'დომოფონის პროექტი',
            'location' => 'თბილისი',
            'final_total' => 1500,
            'notes' => 'შიდა თვითღირებულება და მომწოდებლის პირადი შენიშვნა',
            'calculation' => [
                'quote_engine' => true,
                'service_name' => 'ვიდეოდომოფონი',
                'client_note' => 'შეთავაზება მოქმედებს 7 დღე.',
                'summary' => [
                    'სერვისი' => 'ვიდეოდომოფონი',
                    'პარამეტრები' => '34 ბინა · 1 კარი',
                ],
                'line_items' => [[
                    'label' => 'ვიდეოდომოფონის მონიტორი',
                    'quantity' => 34,
                    'unit' => 'pcs',
                    'unit_cost' => 100,
                    'sell_unit' => 160,
                    'sell_total' => 5440,
                ]],
            ],
        ]);

        $html = view('estimates.pdf', [
            'estimate' => $estimate,
            'calculation' => $estimate->calculation,
            'branding' => [],
            'contact' => [],
        ])->render();

        $this->assertStringContainsString('შეთავაზება მოქმედებს 7 დღე.', $html);
        $this->assertStringContainsString('34 ბინა · 1 კარი', $html);
        $this->assertStringNotContainsString('შიდა თვითღირებულება', $html);
        $this->assertStringNotContainsString('100.00', $html);
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
