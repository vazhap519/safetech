<?php

use App\Http\Controllers\Admin\EstimatePdfController;
use App\Models\CameraPlan;
use App\Models\User;
use App\Support\DeploymentInfo;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

// The reference photo is never exposed through a public storage symlink.
// Only a logged-in admin session can inspect saved layout JSON and private images.
Route::get('/admin/camera-plans/{cameraPlan}/layout.json', function (CameraPlan $cameraPlan) {
        $user = request()->user();
        abort_unless($user instanceof User && $user->canAccessPanel(Filament::getPanel('admin')), 403);

        return response()->json($cameraPlan->layout)
            ->header('Cache-Control', 'private, no-store')
            ->header('X-Robots-Tag', 'noindex, nofollow');
})->name('admin.camera-plans.layout');

Route::get('/admin/camera-plans/{cameraPlan}/background', function (CameraPlan $cameraPlan) {
        $user = request()->user();
        abort_unless($user instanceof User && $user->canAccessPanel(Filament::getPanel('admin')), 403);

        abort_unless($cameraPlan->background_path, 404);

        return Storage::disk('local')
            ->response($cameraPlan->background_path, null, [
                'Cache-Control' => 'private, no-store',
                'X-Robots-Tag' => 'noindex, nofollow',
                'X-Content-Type-Options' => 'nosniff',
            ]);
})->name('admin.camera-plans.background');
