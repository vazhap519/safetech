<?php

use App\Http\Controllers\Api\ContactLeadController;
use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);

Route::post('/contact-leads', ContactLeadController::class)
    ->middleware('throttle:contact-leads')
    ->name('api.contact-leads.store');

Route::post('/analytics/events', AnalyticsEventController::class)
    ->middleware('throttle:analytics-events')
    ->name('api.analytics-events.store');

Route::get('/content', PublicContentController::class)->name('api.content');
Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('api.services.show');
Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('api.projects.show');
