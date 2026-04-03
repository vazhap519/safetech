<?php
////
////namespace App\Http\Controllers\Api;
////
////use App\Http\Controllers\Controller;
////use App\Models\Post;
////use Illuminate\Http\Request;
////use Illuminate\Support\Facades\Cache;
////use Illuminate\Http\JsonResponse;
////
////class BlogController extends Controller
////{
////    /**
////     * 📌 BLOG LIST
////     */
////    public function index(Request $request): JsonResponse
////    {
////        $category = $request->string('category')->toString();
////        $page = $request->integer('page', 1);
////
////        $cacheKey = "blog:index:{$category}:page:{$page}";
////
////        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category) {
////
////            $query = Post::query()
////                ->with([
////                    'category:id,name,slug',
////                    'media'
////                ])
////                ->where('is_published', true);
////
////            // ✅ category filter
////            if ($category && $category !== 'all') {
////                $query->whereHas('category', function ($q) use ($category) {
////                    $q->where('slug', $category);
////                });
////            }
////
////            $posts = $query
////                ->latest()
////                ->paginate(9);
////
////            return [
////                'data' => $posts->getCollection()->map(fn($post) => $this->transformPostCard($post)),
////                'meta' => [
////                    'current_page' => $posts->currentPage(),
////                    'last_page' => $posts->lastPage(),
////                    'per_page' => $posts->perPage(),
////                    'total' => $posts->total(),
////                ]
////            ];
////        });
////
////        return response()->json($data);
////    }
////
////    /**
////     * 📌 SINGLE BLOG
////     */
////    public function show(string $slug): JsonResponse
////    {
////        $cacheKey = "blog:post:{$slug}";
////
////        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
////
////            $post = Post::query()
////                ->with([
////                    'category:id,name,slug',
////                    'author',
////                    'sections',
////                    'media'
////                ])
////                ->where('slug', $slug)
////                ->where('is_published', true)
////                ->firstOrFail();
////
////            return [
////                'data' => $this->transformPostDetail($post),
////            ];
////        });
////
////        return response()->json($data);
////    }
////
////    /**
////     * 🔁 CLEAR CACHE
////     */
////    public function revalidate(): JsonResponse
////    {
////        Cache::flush();
////
////        return response()->json([
////            'success' => true
////        ]);
////    }
////
////    /**
////     * =========================
////     * 🔧 TRANSFORMERS
////     * =========================
////     */
////
////    private function transformPostCard($post): array
////    {
////        return [
////            'title' => $post->title,
////            'slug' => $post->slug,
////
////            'excerpt' => $post->excerpt, // 🔥 ADD
////
////            'image' => $this->getImage($post),
////
////            'reading_time' => $post->reading_time,
////            'published_year' => $post->published_year,
////
////            'created_at' => $post->created_at?->toDateString(), // 🔥 ADD
////
////            'category' => [
////                'name' => $post->category?->name,
////                'slug' => $post->category?->slug,
////            ],
////
////            'author' => $post->author ? [ // 🔥 ADD
////                'name' => $post->author->name,
////            ] : null,
////        ];
////    }
////
////    private function transformPostDetail($post): array
////    {
////        return [
////            'title' => $post->title,
////            'slug' => $post->slug,
////            'excerpt' => $post->excerpt,
////
////            'image' => $this->getImage($post),
////
////            'reading_time' => $post->reading_time,
////            'published_year' => $post->published_year,
////            'seo' => [
////                'title' => $post->seo['title'] ?? $post->title,
////
////                'description' => $post->seo['description']
////                    ?? $post->excerpt,
////
////                'keywords' => collect($post->seo['keywords'] ?? [])
////                    ->pluck('value')
////                    ->values(),
////
////                'content' => collect($post->seo['content'] ?? [])
////                    ->pluck('text')
////                    ->values(),
////
////                'image' => $this->getImage($post),
////            ],
////            'category' => [
////                'name' => $post->category?->name,
////                'slug' => $post->category?->slug,
////            ],
////
////            'author' => $post->author ? [
////                'name' => $post->author->name,
////                'avatar' => $this->getAuthorAvatar($post),
////                'socials' => $post->author->socials,
////            ] : null,
////
////            'sections' => $post->sections
////                ->sortBy('order')
////                ->values(),
////
////            'related' => $this->getRelatedPosts($post),
////        ];
////    }
////
////    /**
////     * =========================
////     * 🧠 HELPERS
////     * =========================
////     */
////
////    private function getImage($post): ?string
////    {
////        try {
////            $media = $post->getFirstMedia('cover');
////
////            return $media
////                ? $media->getUrl('webp')
////                : null;
////
////        } catch (\Throwable $e) {
////            return null; // 🔥 NEVER CRASH
////        }
////    }
////
////    private function getAuthorAvatar($post): ?string
////    {
////        try {
////            return $post->author?->getFirstMediaUrl('avatar') ?: null;
////        } catch (\Throwable $e) {
////            return null;
////        }
////    }
////
////    private function getRelatedPosts($post)
////    {
////        return Post::query()
////            ->where('category_id', $post->category_id)
////            ->where('id', '!=', $post->id)
////            ->where('is_published', true)
////            ->latest()
////            ->limit(3)
////            ->with('media')
////            ->get()
////            ->map(fn($item) => [
////                'title' => $item->title,
////                'slug' => $item->slug,
////                'image' => $this->getImage($item),
////            ]);
////    }
////}
//
//
//namespace App\Http\Controllers\Api;
//
//use App\Http\Controllers\Controller;
//use App\Models\Post;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Cache;
//use Illuminate\Http\JsonResponse;
//
//class BlogController extends Controller
//{
//    public function index(Request $request): JsonResponse
//    {
//        $category = $request->string('category')->toString();
//        $page = $request->integer('page', 1);
//
//        $cacheKey = "blog:index:{$category}:page:{$page}";
//
//        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category) {
//
//            $query = Post::query()
//                ->with([
//                    'category:id,name,slug',
//                    'author:id,name', // 🔥 FIX
//                    'media'
//                ])
//                ->where('is_published', true);
//
//            if ($category && $category !== 'all') {
//                $query->whereHas('category', function ($q) use ($category) {
//                    $q->where('slug', $category);
//                });
//            }
//
//            $posts = $query->latest()->paginate(9);
//
//            return [
//                'data' => $posts->getCollection()->map(fn($post) => $this->transformPostCard($post)),
//                'meta' => [
//                    'current_page' => $posts->currentPage(),
//                    'last_page' => $posts->lastPage(),
//                    'per_page' => $posts->perPage(),
//                    'total' => $posts->total(),
//                ]
//            ];
//        });
//
//        return response()->json($data);
//    }
//
////    public function show(string $slug): JsonResponse
////    {
////        $cacheKey = "blog:post:{$slug}";
////
////        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
////
////            $post = Post::query()
////                ->with([
////                    'category:id,name,slug',
////                    'author',
////                    'sections',
////                    'media'
////                ])
////                ->where('slug', $slug)
////                ->where('is_published', true)
////                ->firstOrFail();
////
////            return [
////                'data' => $this->transformPostDetail($post),
////            ];
////        });
////
////        return response()->json($data);
////    }
//    public function show(string $slug): JsonResponse
//    {
//        $cacheKey = "blog:post:{$slug}";
//
//        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
//
//            $post = Post::query()
//                ->with([
//                    'category:id,name,slug',
//                    'author',
//                    'sections',
//                    'media'
//                ])
//                ->where('slug', $slug)
//                ->where('is_published', true)
//                ->firstOrFail();
//
//            $seo = $post->seo ?? [];
//
//            return [
//                'data' => $this->transformPostDetail($post),
//
//                // 🔥 FULL SEO BLOCK
//                'seo' => [
//                    'meta' => [
//                        'title' => $seo['title'] ?? $post->title,
//                        'description' => $seo['description'] ?? $post->excerpt,
//                        'keywords' => collect($seo['keywords'] ?? [])
//                            ->pluck('value')
//                            ->values(),
//                        'image' => $this->getImage($post),
//                    ],
//
//                    // 🔥 FAQ
//                    'faq' => $post->faq ?? [],
//
//                    // 🔥 CUSTOM SCHEMA
//                    'schema' => $post->schema ?? null,
//                ],
//            ];
//        });
//
//        return response()->json($data);
//    }
//    private function transformPostCard($post): array
//    {
//        return [
//            'title' => $post->title,
//            'slug' => $post->slug,
//
//            'excerpt' => $post->excerpt,
//            'image' => $this->getImage($post),
//
//            'reading_time' => $post->reading_time,
//            'published_year' => $post->published_year,
//            'created_at' => $post->created_at?->toDateString(),
//
//            'category' => [
//                'name' => $post->category?->name,
//                'slug' => $post->category?->slug,
//            ],
//
//            'author' => $post->author ? [
//                'name' => $post->author->name,
//            ] : null,
//        ];
//    }
//
//    private function transformPostDetail($post): array
//    {
//        return [
//            'title' => $post->title,
//            'slug' => $post->slug,
//            'excerpt' => $post->excerpt,
//
//            'image' => $this->getImage($post),
//
//            'reading_time' => $post->reading_time,
//            'published_year' => $post->published_year,
//
//            'seo' => [
//                'title' => $post->seo['title'] ?? $post->title,
//                'description' => $post->seo['description'] ?? $post->excerpt,
//                'keywords' => collect($post->seo['keywords'] ?? [])->pluck('value')->values(),
//                'content' => collect($post->seo['content'] ?? [])->pluck('text')->values(),
//                'image' => $this->getImage($post),
//            ],
//
//            'category' => [
//                'name' => $post->category?->name,
//                'slug' => $post->category?->slug,
//            ],
//
//            'author' => $post->author ? [
//                'name' => $post->author->name,
//                'avatar' => $this->getAuthorAvatar($post),
//            ] : null,
//
//            'sections' => $post->sections->sortBy('position')->values(),
//
//            'related' => $this->getRelatedPosts($post),
//        ];
//    }
//
//    private function getImage($post): ?string
//    {
//        try {
//            $media = $post->getFirstMedia('cover');
//            return $media ? $media->getUrl('webp') : null;
//        } catch (\Throwable $e) {
//            return null;
//        }
//    }
//
//    private function getAuthorAvatar($post): ?string
//    {
//        try {
//            return $post->author?->getFirstMediaUrl('avatar') ?: null;
//        } catch (\Throwable $e) {
//            return null;
//        }
//    }
//
//    private function getRelatedPosts($post)
//    {
//        return Post::query()
//            ->where('category_id', $post->category_id)
//            ->where('id', '!=', $post->id)
//            ->where('is_published', true)
//            ->latest()
//            ->limit(3)
//            ->with('media')
//            ->get()
//            ->map(fn($item) => [
//                'title' => $item->title,
//                'slug' => $item->slug,
//                'image' => $this->getImage($item),
//            ]);
//    }
//}


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 📌 BLOG LIST
    |--------------------------------------------------------------------------
    */
//    public function index(Request $request): JsonResponse
//    {
//        $category = $request->string('category')->toString();
//        $page = $request->integer('page', 1);
//
//        $cacheKey = "blog:index:{$category}:page:{$page}";
//
//        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category) {
//
//            $query = Post::query()
//                ->with([
//                    'category:id,name,slug',
//                    'author:id,name',
//                    'media'
//                ])
//                ->where('is_published', true);
//
//            if ($category && $category !== 'all') {
//                $query->whereHas('category', function ($q) use ($category) {
//                    $q->where('slug', $category);
//                });
//            }
//
//            $posts = $query->latest()->paginate(9);
//
//            return [
//                'data' => $posts->getCollection()
//                    ->map(fn($post) => $this->transformPostCard($post)),
//
//                'meta' => [
//                    'current_page' => $posts->currentPage(),
//                    'last_page' => $posts->lastPage(),
//                    'per_page' => $posts->perPage(),
//                    'total' => $posts->total(),
//                ]
//            ];
//        });
//
//        return response()->json($data);
//    }
//    public function index(Request $request): JsonResponse
//    {
//        $category = $request->get('category') ?? 'all';
//        $page = $request->integer('page', 1);
//
//        $cacheKey = "blog:index:{$category}:page:{$page}";
//
//        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category) {
//
//            $query = Post::query()
//                ->with([
//                    'category:id,name,slug',
//                    'author:id,name',
//                    'media'
//                ])
//                ->where('is_published', true);
//
//            if ($category !== 'all') {
//                $query->whereHas('category', function ($q) use ($category) {
//                    $q->where('slug', $category);
//                });
//            }
//
//            $posts = $query->latest()->paginate(9)->appends([
//                'category' => $category,
//            ]);
//
//            return [
//                'data' => $posts->getCollection()
//                    ->map(fn($post) => $this->transformPostCard($post)),
//
//                'meta' => [
//                    'current_page' => $posts->currentPage(),
//                    'last_page' => $posts->lastPage(),
//                    'per_page' => $posts->perPage(),
//                    'total' => $posts->total(),
//                ],
//
//                'links' => [
//                    'next' => $posts->nextPageUrl(),
//                    'prev' => $posts->previousPageUrl(),
//                ],
//            ];
//        });
//
//        return response()->json($data);
//    }

    public function index(Request $request): JsonResponse
    {
        $category = $request->get('category', 'all');
        $page = (int) $request->get('page', 1);

        $cacheKey = "blog:index:{$category}:page:{$page}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $page) {

            $query = Post::query()
                ->select([
                    'id',
                    'slug',
                    'title',
                    'excerpt',
                    'created_at',
                    'category_id',
                    'author_id',
                ])
                ->with([
                    'category:id,name,slug',
                    'author:id,name',
                    'media',
                ])
                ->where('is_published', true);

            /* =========================
               CATEGORY FILTER
            ========================= */
            if ($category !== 'all') {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            /* =========================
               PAGINATION (IMPORTANT)
            ========================= */
            $posts = $query
                ->latest()
                ->paginate(9, ['*'], 'page', $page);

            return [
                'data' => $posts->getCollection()
                    ->map(fn($post) => $this->transformPostCard($post))
                    ->values(),

                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],

                'links' => [
                    'next' => $posts->hasMorePages()
                        ? url("/api/blog?page=" . ($posts->currentPage() + 1) . ($category !== 'all' ? "&category={$category}" : ""))
                        : null,

                    'prev' => $posts->currentPage() > 1
                        ? url("/api/blog?page=" . ($posts->currentPage() - 1) . ($category !== 'all' ? "&category={$category}" : ""))
                        : null,
                ],
            ];
        });

        return response()->json($data);
    }
    /*
    |--------------------------------------------------------------------------
    | 📌 SINGLE BLOG (SEO FIXED)
    |--------------------------------------------------------------------------
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

            $seo = $post->seo ?? [];

            return [
                'data' => [
                    ...$this->transformPostDetail($post),

                    // ✅ SINGLE SOURCE OF TRUTH
                    'seo' => [
                        'meta' => [
                            'title' => $seo['title'] ?? $post->title,

                            'description' => $seo['description']
                                ?? $post->excerpt,

                            'keywords' => collect($seo['keywords'] ?? [])
                                ->pluck('value')
                                ->filter(fn($k) => !empty($k))
                                ->values(),

                            'image' => $this->getImage($post),
                        ],

                        // 🔥 FAQ (ready for JSON-LD)
                        'faq' => $post->faq ?? [],

                        // 🔥 CUSTOM SCHEMA (already JSON)
                        'schema' => $post->schema ?? null,
                    ],
                ],
            ];
        });

        return response()->json($data);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔧 TRANSFORMERS
    |--------------------------------------------------------------------------
    */

    private function transformPostCard($post): array
    {
        return [
            'title' => $post->title,
            'slug' => $post->slug,

            'excerpt' => $post->excerpt,

            'image' => $this->getImage($post),

            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,

            'created_at' => $post->created_at?->toDateString(),

            'category' => [
                'name' => $post->category?->name,
                'slug' => $post->category?->slug,
            ],

            'author' => $post->author ? [
                'name' => $post->author->name,
            ] : null,
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

            // ❌ SEO აქ აღარ არის (IMPORTANT)

            'category' => [
                'name' => $post->category?->name,
                'slug' => $post->category?->slug,
            ],

            'author' => $post->author ? [
                'name' => $post->author->name,
                'avatar' => $this->getAuthorAvatar($post),
            ] : null,

            'sections' => $post->sections
                ->sortBy('position')
                ->values(),

            'related' => $this->getRelatedPosts($post),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 🧠 HELPERS
    |--------------------------------------------------------------------------
    */

    private function getImage($post): ?string
    {
        try {
            $media = $post->getFirstMedia('cover');

            return $media
                ? $media->getFullUrl('webp') // ✅ unified
                : null;

        } catch (\Throwable $e) {
            return null;
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
