<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ServicesController;

use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class,'index']);
Route::get('/services', [ServicesController::class, 'index']);
Route::get('/services/{slug}', [ServicesController::class, 'show']);
Route::get('/settings', [SettingsController::class, 'index']);
