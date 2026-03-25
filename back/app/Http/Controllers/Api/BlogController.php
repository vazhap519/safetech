<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * 📌 BLOG LIST
     */
    public function index(Request $request): JsonResponse
    {
        $category = $request->string('category')->toString();
        $page = $request->integer('page', 1);

        $cacheKey = "blog:index:{$category}:page:{$page}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category) {

            $query = Post::query()
                ->with([
                    'category:id,name,slug',
                    'media'
                ])
                ->where('is_published', true);

            // ✅ category filter
            if ($category && $category !== 'all') {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            $posts = $query
                ->latest()
                ->paginate(9);

            return [
                'data' => $posts->getCollection()->map(fn($post) => $this->transformPostCard($post)),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ];
        });

        return response()->json($data);
    }

    /**
     * 📌 SINGLE BLOG
     */
    public function show(string $slug): JsonResponse
    {
        $cacheKey = "blog:post:{$slug}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {

            $post = Post::query()
                ->with([
                    'category:id,name,slug',
                    'author',
                    'sections',
                    'media'
                ])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            return [
                'data' => $this->transformPostDetail($post),
            ];
        });

        return response()->json($data);
    }

    /**
     * 🔁 CLEAR CACHE
     */
    public function revalidate(): JsonResponse
    {
        Cache::flush();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * =========================
     * 🔧 TRANSFORMERS
     * =========================
     */

    private function transformPostCard($post): array
    {
        return [
            'title' => $post->title,
            'slug' => $post->slug,

            // ✅ SAFE IMAGE
            'image' => $this->getImage($post),

            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,

            'category' => [
                'name' => $post->category?->name,
                'slug' => $post->category?->slug,
            ],
        ];
    }

    private function transformPostDetail($post): array
    {
        return [
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,

            'image' => $this->getImage($post),

            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,

            'meta' => [
                'title' => $post->meta_title ?: $post->title,
                'description' => $post->meta_description,
                'image' => $this->getImage($post),
            ],

            'category' => [
                'name' => $post->category?->name,
                'slug' => $post->category?->slug,
            ],

            'author' => $post->author ? [
                'name' => $post->author->name,
                'avatar' => $this->getAuthorAvatar($post),
                'socials' => $post->author->socials,
            ] : null,

            'sections' => $post->sections
                ->sortBy('order')
                ->values(),

            'related' => $this->getRelatedPosts($post),
        ];
    }

    /**
     * =========================
     * 🧠 HELPERS
     * =========================
     */

    private function getImage($post): ?string
    {
        try {
            $media = $post->getFirstMedia('cover');

            return $media
                ? $media->getUrl('webp')
                : null;

        } catch (\Throwable $e) {
            return null; // 🔥 NEVER CRASH
        }
    }

    private function getAuthorAvatar($post): ?string
    {
        try {
            return $post->author?->getFirstMediaUrl('avatar') ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function getRelatedPosts($post)
    {
        return Post::query()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest()
            ->limit(3)
            ->with('media')
            ->get()
            ->map(fn($item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'image' => $this->getImage($item),
            ]);
    }
}
