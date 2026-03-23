<?php

use App\Http\Controllers\Api\ServicesController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\HomeController;
Route::get('/',[HomeController::class,'index']);
Route::get('/services', [ServicesController::class, 'index']);
Route::get('/services/{slug}', [ServicesController::class, 'show']);
