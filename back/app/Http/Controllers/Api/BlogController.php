<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\MultilingualContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $category = $request->string('category')->toString();
        $page = $request->integer('page', 1);
        $locale = $this->locale($request);

        $cacheKey = "blog:index:{$locale}:{$category}:page:{$page}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $locale) {
            $query = Post::query()
                ->with(['category:id,name,slug,translations', 'media'])
                ->where('is_published', true);

            if ($category && $category !== 'all') {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            }

            $posts = $query->latest()->paginate(9);

            return [
                'data' => $posts->getCollection()->map(fn ($post) => $this->transformPostCard($post, $locale)),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ];
        });

        return response()->json($data);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->locale($request);
        $cacheKey = "blog:post:{$locale}:{$slug}";

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug, $locale) {
            $post = Post::query()
                ->with([
                    'category:id,name,slug,translations',
                    'author',
                    'sections',
                    'media',
                ])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            return [
                'data' => $this->transformPostDetail($post, $locale),
            ];
        });

        return response()->json($data);
    }

    public function revalidate(): JsonResponse
    {
        Cache::flush();

        return response()->json(['success' => true]);
    }

    private function transformPostCard(Post $post, string $locale): array
    {
        return [
            'title' => $this->translated($post, 'title', $post->title, $locale),
            'slug' => $post->slug,
            'excerpt' => $this->translated($post, 'excerpt', $post->excerpt, $locale),
            'image' => $this->getImage($post),
            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,
            'category' => [
                'name' => $post->category
                    ? $this->translated($post->category, 'name', $post->category->name, $locale)
                    : null,
                'slug' => $post->category?->slug,
            ],
        ];
    }

    private function transformPostDetail(Post $post, string $locale): array
    {
        $title = $this->translated($post, 'title', $post->title, $locale);
        $excerpt = $this->translated($post, 'excerpt', $post->excerpt, $locale);
        $metaTitle = $this->translated($post, 'metaTitle', $post->meta_title ?: $post->title, $locale);
        $metaDescription = $this->translated($post, 'metaDescription', $post->meta_description, $locale);

        return [
            'title' => $title,
            'slug' => $post->slug,
            'excerpt' => $excerpt,
            'image' => $this->getImage($post),
            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,
            'meta' => [
                'title' => $metaTitle ?: $title,
                'description' => $metaDescription ?: $excerpt,
                'image' => $this->getImage($post),
            ],
            'category' => [
                'name' => $post->category
                    ? $this->translated($post->category, 'name', $post->category->name, $locale)
                    : null,
                'slug' => $post->category?->slug,
            ],
            'author' => $post->author ? [
                'name' => $this->translated($post->author, 'name', $post->author->name, $locale),
                'avatar' => $this->getAuthorAvatar($post),
                'socials' => $post->author->socials,
            ] : null,
            'sections' => $post->sections
                ->sortBy('position')
                ->values()
                ->map(fn ($section) => [
                    'id' => $section->id,
                    'title' => $this->translated($section, 'title', $section->title, $locale),
                    'content' => $this->translated($section, 'content', $section->content, $locale),
                    'position' => $section->position,
                ]),
            'related' => $this->getRelatedPosts($post, $locale),
        ];
    }

    private function getImage(Post $post): ?string
    {
        try {
            $media = $post->getFirstMedia('cover');

            return $media ? $media->getUrl('webp') : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function getAuthorAvatar(Post $post): ?string
    {
        try {
            return $post->author?->getFirstMediaUrl('avatar') ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function getRelatedPosts(Post $post, string $locale)
    {
        return Post::query()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest()
            ->limit(3)
            ->with('media')
            ->get()
            ->map(fn (Post $item) => [
                'title' => $this->translated($item, 'title', $item->title, $locale),
                'slug' => $item->slug,
                'image' => $this->getImage($item),
            ]);
    }

    private function locale(Request $request): string
    {
        $locale = $request->string('locale')->toString();

        return in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
    }

    private function translated($model, string $field, mixed $fallback, string $locale): string
    {
        $values = MultilingualContent::valuesForField($model, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }
}
