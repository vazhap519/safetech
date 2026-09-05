<?php

use App\Http\Controllers\Admin\EstimatePdfController;
use App\Support\DeploymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name', 'Safetech API'),
        'status' => 'ok',
    ]);
});

Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow: /\n",
    200,
    [
        'Content-Type' => 'text/plain; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow, nosnippet',
    ],
))->name('api-host.robots');

Route::get('/_safetech/upload-probe', function (Request $request) {
    $nonce = trim((string) $request->header('X-SafeTech-Upload-Probe-Nonce'));
    $providedSignature = trim((string) $request->header('X-SafeTech-Upload-Probe-Signature'));
    $appKey = (string) config('app.key');
    $expectedSignature = $nonce !== '' && $appKey !== ''
        ? hash_hmac('sha256', $nonce, $appKey)
        : '';

    abort_unless(
        $expectedSignature !== ''
        && $providedSignature !== ''
        && hash_equals($expectedSignature, $providedSignature),
        404,
    );

    return response()->json([
        'status' => 'ok',
        'commit' => DeploymentInfo::commit(),
        'request_root' => $request->root(),
        'csrf_token' => csrf_token(),
        'livewire_upload_url' => app(GenerateSignedUploadUrl::class)->forLocal(),
    ])->withHeaders([
        'Cache-Control' => 'no-store, private',
        'X-Robots-Tag' => 'noindex, nofollow, nosnippet',
    ]);
})->name('safetech.upload-probe');

Route::get('/admin/estimates/{estimate}/pdf', EstimatePdfController::class)
    ->name('admin.estimates.pdf');
