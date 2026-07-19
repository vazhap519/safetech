<?php

use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactLeadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectCategoryController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServiceCalculatorProfileController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);

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
Route::get('/privacy', [PrivacyController::class, 'index'])->name('api.privacy.index');
Route::get('/home', [HomeController::class, 'index'])->name('api.home.index');
Route::get('/about', [AboutController::class, 'index'])->name('api.about.index');
Route::get('/blog', [BlogController::class, 'index'])->name('api.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('api.blog.show');
Route::get('/categories', CategoryController::class)->name('api.categories.index');
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
