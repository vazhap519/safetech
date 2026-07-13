<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

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

        $pdf = Pdf::loadView('estimates.pdf', [
            'estimate' => $estimate,
            'calculation' => $estimate->calculation ?? [],
            'branding' => $branding,
            'contact' => $contact,
        ])->setPaper('a4');

        return $pdf->download("{$estimate->estimate_number}.pdf");
    }
}
