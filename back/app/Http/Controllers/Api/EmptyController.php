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

        if (!$data) {
            return response()->json([
                'title' => null,
                'description' => null,
                'coming_soon' => null,
                'social' => [],
            ]);
        }

        return response()->json($data);
    }
}
