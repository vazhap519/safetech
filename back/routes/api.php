<?php

use App\Http\Controllers\Api\AboutController;
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


// 🔥 REVALIDATE ROUTES
Route::post('/home/revalidate', [HomeController::class, 'revalidate']);

// 🔥 ABOUT UPDATE (თუ იყენებ API-დან)
Route::post('/about', [AboutController::class, 'update']);
Route::post('/services/revalidate', [ServicesController::class, 'revalidate']);
Route::post('/services/{slug}/revalidate', [ServicesController::class, 'revalidateSingle']);
Route::get('/privacy', [PrivacyController::class, 'index']);
Route::post('/privacy', [PrivacyController::class, 'update']);
