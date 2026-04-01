<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmptyPage;
use Illuminate\Http\Request;

class EmptyController extends Controller
{
    public function index()
    {
        $data = EmptyPage::first();

        return response()->json([
            'data' => $data,
        ]);
    }
}
