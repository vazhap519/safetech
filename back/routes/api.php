<?php

use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactLeadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use App\Models\Category;
use App\Support\MultilingualContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);

Route::post('/contact-leads', ContactLeadController::class)
    ->middleware('throttle:contact-leads')
    ->name('api.contact-leads.store');

Route::post('/analytics/events', AnalyticsEventController::class)
    ->middleware('throttle:analytics-events')
    ->name('api.analytics-events.store');

Route::get('/content', PublicContentController::class)->name('api.content');
Route::get('/settings', [SettingsController::class, 'index'])->name('api.settings.index');
Route::get('/privacy', [PrivacyController::class, 'index'])->name('api.privacy.index');
Route::get('/home', [HomeController::class, 'index'])->name('api.home.index');
Route::get('/about', [AboutController::class, 'index'])->name('api.about.index');
Route::get('/blog', [BlogController::class, 'index'])->name('api.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('api.blog.show');
Route::get('/categories', function (Request $request) {
    $locale = $request->string('locale')->toString();
    $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';

    return response()->json([
        'data' => Category::query()
            ->select(['id', 'name', 'slug', 'translations'])
            ->orderBy('name')
            ->get()
            ->map(function (Category $category) use ($locale) {
                $values = MultilingualContent::valuesForField($category, 'name', $category->name);

                return [
                    'id' => $category->id,
                    'name' => $values[$locale] ?: $category->name,
                    'slug' => $category->slug,
                ];
            }),
    ]);
})->name('api.categories.index');
Route::get('/services', [ServiceController::class, 'index'])->name('api.services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('api.services.show');
Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('api.projects.show');
