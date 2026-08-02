<?php

use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\ContactLeadController;
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\ServiceCalculatorProfileController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use App\Support\DeploymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('/health', function (Request $request): array {
    $payload = [
        'status' => 'ok',
        'commit' => DeploymentInfo::commit(),
    ];

    $nonce = trim((string) $request->header('X-SafeTech-Upload-Probe-Nonce'));
    $providedSignature = trim((string) $request->header('X-SafeTech-Upload-Probe-Signature'));
    $appKey = (string) config('app.key');
    $expectedSignature = $nonce !== '' && $appKey !== ''
        ? hash_hmac('sha256', $nonce, $appKey)
        : '';

    if (
        $expectedSignature !== ''
        && $providedSignature !== ''
        && hash_equals($expectedSignature, $providedSignature)
    ) {
        $payload['request_root'] = $request->root();
        $payload['livewire_upload_url'] = URL::temporarySignedRoute(
            'livewire.upload-file',
            now()->addMinutes(5),
        );
    }

    return $payload;
});

Route::post('/contact-leads', ContactLeadController::class)
    ->middleware('throttle:contact-leads')
    ->name('api.contact-leads.store');

Route::post('/analytics/events', AnalyticsEventController::class)
    ->middleware('throttle:analytics-events')
    ->name('api.analytics-events.store');

Route::get('/content', PublicContentController::class)->name('api.content');
Route::get('/seo', [SeoController::class, 'index'])->name('api.seo.index');
Route::get('/seo/{key}', [SeoController::class, 'show'])->name('api.seo.show');
Route::get('/settings', [SettingsController::class, 'index'])->name('api.settings.index');
Route::get('/service-categories', ServiceCategoryController::class)
    ->name('api.service-categories.index');
Route::get('/project-categories', ProjectCategoryController::class)
    ->name('api.project-categories.index');

Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('api.services.show');
Route::get('/service-calculator/profiles', ServiceCalculatorProfileController::class)
    ->name('api.service-calculator.profiles');
Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('api.projects.show');
