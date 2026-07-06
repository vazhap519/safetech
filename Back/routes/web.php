<?php

use App\Http\Controllers\Admin\EstimatePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/estimates/{estimate}/pdf', EstimatePdfController::class)
    ->name('admin.estimates.pdf');
