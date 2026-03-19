<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\SiteSettingController;

Route::get('/settings', [SiteSettingController::class, 'index']);
Route::get('/home',[HomeController::class,'index']);