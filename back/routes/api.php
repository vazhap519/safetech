<?php

use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\ContactLeadController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\ReviewInvitationController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\ServiceCalculatorProfileController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use App\Support\DeploymentInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (): JsonResponse {
    try {
        DB::connection()->getPdo();
    } catch (Throwable) {
        return response()->json([
            'status' => 'unavailable',
            'commit' => DeploymentInfo::commit(),
            'database' => 'unavailable',
        ], 503);
    }

    return response()->json([
        'status' => 'ok',
        'commit' => DeploymentInfo::commit(),
        'database' => 'ok',
    ]);
});

Route::post('/contact-leads', ContactLeadController::class)
    ->middleware('throttle:contact-leads')
    ->name('api.contact-leads.store');

Route::get('/review-invitations/{token}', [ReviewInvitationController::class, 'show'])
    ->name('api.review-invitations.show');
Route::post('/review-invitations/{token}/submit', [ReviewInvitationController::class, 'submit'])
    ->middleware('throttle:review-invitations')
    ->name('api.review-invitations.submit');

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
Route::get('/services/options', [ServiceController::class, 'options'])->name('api.services.options');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('api.services.show');
Route::get('/service-calculator/profiles', ServiceCalculatorProfileController::class)
    ->name('api.service-calculator.profiles');
Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('api.projects.show');
Route::get('/pages', [PageController::class, 'index'])->name('api.pages.index');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('api.pages.show');
