<?php

use App\Http\Controllers\Admin\EstimatePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name', 'Safetech API'),
        'status' => 'ok',
    ]);
});

Route::get('/admin/estimates/{estimate}/pdf', EstimatePdfController::class)
    ->name('admin.estimates.pdf');
