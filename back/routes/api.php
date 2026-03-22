<?php
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\HomeController;
Route::get('/',[HomeController::class,'index']);