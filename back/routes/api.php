<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PrivacyController;
use App\Http\Controllers\Api\ServicesController;

use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class,'index']);
Route::get('/services', [ServicesController::class, 'index']);
Route::get('/services/{slug}', [ServicesController::class, 'show']);
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/privacy', [PrivacyController::class, 'index']);
Route::get('/about', [AboutController::class, 'index']);
Route::get('/blog', [\App\Http\Controllers\Api\BlogController::class, 'index']);
Route::get('/blog/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);

// 🔥 REVALIDATE ROUTES
Route::post('/home/revalidate', [HomeController::class, 'revalidate']);

// 🔥 ABOUT UPDATE (თუ იყენებ API-დან)
Route::post('/about', [AboutController::class, 'update']);
Route::post('/services/revalidate', [ServicesController::class, 'revalidate']);
Route::post('/services/{slug}/revalidate', [ServicesController::class, 'revalidateSingle']);
Route::get('/privacy', [PrivacyController::class, 'index']);
Route::post('/privacy', [PrivacyController::class, 'update']);
Route::post('/blog/revalidate', [BlogController::class, 'revalidate']);
Route::post('/blog/{slug}/revalidate', [BlogController::class, 'revalidateSingle']);
Route::get('/categories', function () {
    return \App\Models\Category::whereHas('posts', function ($q) {
        $q->where('is_published', true);
    })->select('name', 'slug')->get();
});
