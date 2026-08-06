<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(Page::query()->publiclyVisible()->get());
    }

    public function show(Request $request, string $slug): PageResource|JsonResponse
    {
        $page = Page::query()->publiclyVisible()->where('slug', $slug)->first();

        if (! $page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return new PageResource($page);
    }
}
