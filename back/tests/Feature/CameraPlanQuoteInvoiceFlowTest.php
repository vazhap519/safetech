<?php

namespace Tests\Feature;

use App\Filament\Pages\QuoteEnginePage;
use App\Filament\Resources\EstimateResource\Pages\EditEstimate;
use App\Models\CameraPlan;
use App\Models\Estimate;
use App\Models\Service;
use App\Models\User;
use App\Support\Calculators\CanonicalServiceCalculator;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CameraPlanQuoteInvoiceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_camera_plan_becomes_a_vat_invoice_and_downloadable_pdf(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $service = Service::query()->create([
            'slug' => 'security-camera-installation',
            'name' => 'CCTV',
            'title' => 'CCTV',
            'description' => 'CCTV installation',
            'seo_description' => 'CCTV installation',
            'is_published' => true,
            'lead_form' => CanonicalServiceCalculator::for('security-camera-installation', 'CCTV'),
        ]);
        $plan = CameraPlan::query()->create([
            'title' => 'Warehouse camera plan',
            'contact_name' => 'Test Client',
            'contact_phone' => '+995555000000',
            'contact_email' => 'client@example.com',
            'status' => 'new',
            'privacy_accepted_at' => now(),
            'layout' => [
                'recordingDays' => 30,
                'storageReservePercent' => 20,
                'cameras' => [
                    ['megapixels' => 4, 'fps' => 15, 'codec' => 'h265', 'bitrateMbps' => 4, 'recordingMode' => 'continuous'],
                    ['megapixels' => 8, 'fps' => 20, 'codec' => 'h265plus', 'bitrateMbps' => 8, 'recordingMode' => 'motion', 'hoursPerDay' => 24, 'motionPercent' => 40],
                ],
            ],
        ]);

        Livewire::withQueryParams(['camera_plan' => $plan->getKey()])
            ->test(QuoteEnginePage::class)
            ->assertSet('serviceId', $service->getKey())
            ->assertSet('clientName', 'Test Client')
            ->assertSet('values.camera_count', 2)
            ->assertSet('values.recording_days', 30)
            ->assertSet('values.required_storage_tb', fn ($value): bool => (float) $value > 0)
            ->set('laborPrice', 250)
            ->call('saveQuote');

        $estimate = Estimate::query()->latest('id')->firstOrFail();
        $this->assertSame($service->getKey(), $estimate->service_id);
        $this->assertSame('Test Client', $estimate->client_name);
        $this->assertGreaterThan(0, (float) data_get($estimate->configuration, 'values.required_storage_tb'));
        $this->assertNotEmpty(data_get($estimate->calculation, 'line_items'));

        Livewire::test(EditEstimate::class, ['record' => $estimate->getRouteKey()])
            ->fillForm([
                'vat_included' => true,
                'vat_rate' => 18,
                'due_date' => now()->addDays(10)->toDateString(),
                'payment_status' => 'unpaid',
                'bank_name' => 'Test Bank',
                'bank_account' => 'GE00TB0000000000000000',
                'payment_reference' => 'CAMERA-PLAN-TEST',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $estimate->refresh();
        $this->assertGreaterThan(0, (float) $estimate->vat_amount);
        $this->assertSame('Test Bank', $estimate->bank_name);

        $this->get(route('admin.estimates.pdf', $estimate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-robots-tag', 'noindex, nofollow, noarchive');

        Livewire::test(EditEstimate::class, ['record' => $estimate->getRouteKey()])
            ->fillForm(['payment_status' => 'partial'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('partial', $estimate->refresh()->payment_status);

        Livewire::test(EditEstimate::class, ['record' => $estimate->getRouteKey()])
            ->fillForm(['payment_status' => 'paid'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('paid', $estimate->refresh()->payment_status);
    }
}
