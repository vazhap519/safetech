<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Response;
use Throwable;

class EstimatePdfController extends Controller
{
    public function __invoke(Estimate $estimate): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $branding = SiteSetting::query()
            ->where('key', 'branding')
            ->first()
            ?->value ?? [];
        $contact = SiteSetting::query()
            ->where('key', 'contact')
            ->first()
            ?->value ?? [];

        $paymentPayload = implode("\n", array_filter([
            "SafeTech invoice {$estimate->estimate_number}",
            'Amount: '.number_format((float) $estimate->final_total, 2, '.', '').' GEL',
            $estimate->bank_name ? "Bank: {$estimate->bank_name}" : null,
            $estimate->bank_account ? "IBAN: {$estimate->bank_account}" : null,
            'Reference: '.($estimate->payment_reference ?: $estimate->estimate_number),
        ]));
        try {
            $paymentQr = (new QRCode)->render($paymentPayload);
        } catch (Throwable) {
            $paymentQr = null;
        }

        $pdf = Pdf::loadView('estimates.pdf', [
            'estimate' => $estimate,
            'calculation' => $estimate->calculation ?? [],
            'branding' => $branding,
            'contact' => $contact,
            'paymentQr' => $paymentQr,
        ])->setPaper('a4');

        $response = $pdf->download("{$estimate->estimate_number}.pdf");
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return $response;
    }
}
