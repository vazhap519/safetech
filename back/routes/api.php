<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactSectionController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\ServicesController;

use App\Http\Controllers\Api\SettingsController;
use App\Models\Category;
use App\Models\CategoryForService;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/blog', [\App\Http\Controllers\Api\BlogController::class, 'index'])->name('blog');
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/contact-page', [ContactSectionController::class, 'index']);

Route::get('/seo', [SeoController::class, 'index']);
Route::get('/seo/model/{type}/{id}', [SeoController::class, 'model']);
Route::get('/seo/{key}', [SeoController::class, 'show']);
Route::get('/services/{slug}', [ServicesController::class, 'show'])->name('service');
Route::get('/blog/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);

Route::get('/empty', [\App\Http\Controllers\Api\EmptyController::class, 'index'])->name('empty');
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/projects', [ProjectsController::class, 'index']);
Route::get('/project-categories', [ProjectsController::class, 'categories']);
Route::get('/projects/{slug}', [ProjectsController::class, 'show']);
Route::get('/projects/{slug}/related', [ProjectsController::class, 'related']);

Route::get('/service-categories', function () {
    $columns = array_values(array_filter([
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'noindex',
    ], fn ($column) => Schema::hasColumn('category_for_services', $column)));

    return CategoryForService::query()
        ->whereHas('services')
        ->select($columns)
        ->orderBy('name')
        ->get();
});
Route::get('/seo-links', function () {

    $services = Service::query()
        ->with('category:id,name,slug')
        ->select('id', 'title', 'slug', 'category_for_service_id')
        ->latest()
        ->get();

    return $services->map(function ($service) {
        return [
            'keywords' => array_values(array_filter([
                $service->title,
                $service->category?->name,
            ])),
            'url' => '/services/' . $service->slug,
            'priority' => 1,
        ];
    })->filter(fn ($item) => !empty($item['keywords']))->values();
});
// 🔥 REVALIDATE ROUTES
Route::post('/home/revalidate', [HomeController::class, 'revalidate']);

// 🔥 ABOUT UPDATE (თუ იყენებ API-დან)
Route::post('/about', [AboutController::class, 'update']);
Route::post('/services/revalidate', [ServicesController::class, 'revalidate']);
Route::post('/services/{slug}/revalidate', [ServicesController::class, 'revalidateSingle']);
Route::post('/privacy', [PrivacyController::class, 'update']);
Route::post('/blog/revalidate', [BlogController::class, 'revalidate']);
Route::post('/blog/{slug}/revalidate', [BlogController::class, 'revalidateSingle']);
Route::get('/categories', function () {
    $columns = array_values(array_filter([
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'intro_text',
        'faq',
        'schema',
        'noindex',
    ], fn ($column) => Schema::hasColumn('categories', $column)));

    return Category::whereHas('posts', function ($q) {
        $q->where('is_published', true);
    })->select($columns)->orderBy('name')->get();
});

Route::get('/manifest.json', function () {
    $settings = Setting::first();
    $icons = collect([
        [
            "src" => $settings?->getFirstMediaUrl('favicon', 'android_192'),
            "sizes" => "192x192",
            "type" => "image/png"
        ],
        [
            "src" => $settings?->getFirstMediaUrl('favicon', 'android_512'),
            "sizes" => "512x512",
            "type" => "image/png"
        ]
    ])->filter(fn ($icon) => !empty($icon['src']))->values()->all();

    return response()->json([
        "name" => config('app.name'),
        "short_name" => config('app.name'),
        "start_url" => "/",
        "display" => "standalone",
        "background_color" => "#ffffff",
        "theme_color" => "#000000",
        "icons" => $icons,
    ]);
});
