<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\SocialLinks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $category = $request->get('category', 'all');
        $page = max(1, (int) $request->get('page', 1));
        $cacheKey = "blog:index:{$category}:page:{$page}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $page) {
            $query = Post::query()
                ->select([
                    'id',
                    'slug',
                    'title',
                    'excerpt',
                    'created_at',
                    'updated_at',
                    'category_id',
                    'author_id',
                    'reading_time',
                    'published_year',
                ])
                ->with([
                    'category:id,name,slug',
                    'author:id,name',
                    'media',
                ])
                ->where('is_published', true);

            if ($category !== 'all') {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            }

            $posts = $query->latest()->paginate(9, ['*'], 'page', $page);

            return [
                'data' => $posts->getCollection()
                    ->map(fn ($post) => $this->transformPostCard($post))
                    ->values(),

                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],

                'links' => [
                    'next' => $posts->hasMorePages()
                        ? url('/api/blog?page=' . ($posts->currentPage() + 1) . ($category !== 'all' ? "&category={$category}" : ''))
                        : null,
                    'prev' => $posts->currentPage() > 1
                        ? url('/api/blog?page=' . ($posts->currentPage() - 1) . ($category !== 'all' ? "&category={$category}" : ''))
                        : null,
                ],
            ];
        });

        return response()->json($data);
    }

    public function show(string $slug): JsonResponse
    {
        $cacheKey = "blog:post:{$slug}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
            $post = Post::query()
                ->with([
                    'category:id,name,slug',
                    'author',
                    'sections',
                    'media',
                ])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            $seo = $post->seo ?? [];
            $url = SocialLinks::frontendUrl("/blog/{$post->slug}");
            $settings = settings();
            $shareButtons = SocialLinks::shareButtons($settings?->share_buttons ?? []);

            return [
                'data' => [
                    ...$this->transformPostDetail($post),
                    'seo' => [
                        'meta' => [
                            'title' => data_get($seo, 'title', $post->title),
                            'description' => data_get($seo, 'description', $post->excerpt),
                            'keywords' => collect(data_get($seo, 'keywords', []))
                                ->map(fn ($item) => is_array($item) ? ($item['value'] ?? null) : $item)
                                ->filter()
                                ->values(),
                            'image' => $this->getImage($post),
                            'canonical' => data_get($seo, 'canonical', $url),
                            'noindex' => (bool) data_get($seo, 'noindex', false),
                        ],
                        'faq' => $post->faq ?? [],
                        'schema' => $post->schema ?? data_get($seo, 'schema'),
                    ],
                ],
                'share' => [
                    'title' => $settings?->share_title ?? '',
                    'share_title' => $settings?->share_title ?? '',
                    'url' => $url,
                    'buttons' => $shareButtons,
                    'share_buttons' => $shareButtons,
                ],
            ];
        });

        return response()->json($data);
    }

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
            'updated_at' => $post->updated_at?->toDateString(),
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
            'created_at' => $post->created_at?->toDateString(),
            'updated_at' => $post->updated_at?->toDateString(),
            'faq' => $post->faq ?? [],
            'category' => [
                'name' => $post->category?->name,
                'slug' => $post->category?->slug,
            ],
            'author' => $post->author ? [
                'name' => $post->author->name,
                'avatar' => $this->getAuthorAvatar($post),
            ] : null,
            'sections' => $post->sections->sortBy('position')->values(),
            'related' => $this->getRelatedPosts($post),
        ];
    }

    private function getImage($post): ?string
    {
        try {
            $media = $post->getFirstMedia('cover');

            return $media ? $media->getFullUrl('webp') : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function getAuthorAvatar($post): ?string
    {
        try {
            return $post->author?->getFirstMediaUrl('avatar') ?: null;
        } catch (\Throwable) {
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
            ->map(fn ($item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'image' => $this->getImage($item),
            ]);
    }

    public function revalidate(): JsonResponse
    {
        Cache::flush();
        $this->notifyFrontendRevalidate('blog');

        return response()->json([
            'success' => true,
            'message' => 'Blog cache cleared',
        ]);
    }

    public function revalidateSingle(string $slug): JsonResponse
    {
        Cache::forget("blog:post:{$slug}");
        $this->notifyFrontendRevalidate("post-{$slug}");

        return response()->json([
            'success' => true,
            'message' => 'Blog post cache cleared',
        ]);
    }

    private function notifyFrontendRevalidate(string $tag): void
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', '')), '/');

        if (!$frontendUrl) {
            return;
        }

        try {
            Http::timeout(3)->post("{$frontendUrl}/api/revalidate", [
                'tag' => $tag,
            ]);
        } catch (\Throwable) {
            //
        }
    }
}
