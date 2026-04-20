<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name', 'Safetech API'),
        'status' => 'ok',
    ]);
});
