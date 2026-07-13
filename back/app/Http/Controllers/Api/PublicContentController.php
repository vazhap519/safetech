<?php

namespace App\Http\Controllers\Api;

use App\Application\Content\PublicContentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class PublicContentController extends Controller
{
    public function __invoke(PublicContentService $service): JsonResponse
    {
        return response()->json(['data' => $service->bootstrap()]);
    }
}
