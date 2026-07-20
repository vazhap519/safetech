<?php

use App\Http\Controllers\Admin\EstimatePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name', 'Safetech API'),
        'status' => 'ok',
    ]);
});

Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow: /\n",
    200,
    [
        'Content-Type' => 'text/plain; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow, nosnippet',
    ],
))->name('api-host.robots');

Route::get('/admin/estimates/{estimate}/pdf', EstimatePdfController::class)
    ->name('admin.estimates.pdf');
