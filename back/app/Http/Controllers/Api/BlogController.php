<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\MultilingualContent;
use App\Support\PublicContentCache;
use App\Support\PublicContentEligibility;
use App\Support\SafeHtml;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function __construct(private readonly SafeHtml $safeHtml) {}

    public function index(Request $request): JsonResponse
    {
        $category = $request->string('category')->toString();
        $page = max(1, $request->integer('page', 1));
        $locale = $this->locale($request);

        $cacheKey = PublicContentCache::key("blog:index:{$locale}:{$category}:all");

        $posts = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($category, $locale) {
            $query = Post::query()
                ->with([
                    'category:id,name,slug,translations',
                    'media',
                    'sections:id,post_id,content,translations',
                ])
                ->publiclyVisible();

            if ($category && $category !== 'all') {
                $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            }

            return $query
                ->latest()
                ->get()
                ->filter(fn (Post $post): bool => PublicContentEligibility::post($post, $locale))
                ->map(fn (Post $post): array => $this->transformPostCard($post, $locale))
                ->values()
                ->all();
        });

        $pageSize = 9;
        $total = count($posts);
        $data = [
            'data' => array_slice($posts, ($page - 1) * $pageSize, $pageSize),
            'meta' => [
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $pageSize)),
                'per_page' => $pageSize,
                'total' => $total,
            ],
        ];

        return response()->json($data);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->locale($request);
        $cacheKey = PublicContentCache::key("blog:post:{$locale}:{$slug}");

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug, $locale) {
            $post = Post::query()
                ->with([
                    'category:id,name,slug,translations',
                    'author',
                    'sections',
                    'media',
                ])
                ->where('slug', $slug)
                ->publiclyVisible()
                ->firstOrFail();

            abort_unless(PublicContentEligibility::post($post, $locale), 404);

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
            'created_at' => $post->created_at?->toAtomString(),
            'updated_at' => $post->updated_at?->toAtomString(),
            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,
            'meta' => [
                'noindex' => (bool) $post->noindex,
            ],
            'has_content' => true,
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
        $localizedKeywords = data_get($post->translations, "keywords.{$locale}");
        $sections = $post->sections
            ->sortBy('position')
            ->values()
            ->map(fn ($section) => [
                'id' => $section->id,
                'title' => $this->translated($section, 'title', $section->title, $locale),
                'content' => $this->safeHtml->sanitize(
                    $this->translated($section, 'content', $section->content, $locale),
                ),
                'position' => $section->position,
            ])
            ->filter(fn (array $section): bool => PublicContentEligibility::hasMeaningfulText($section['content']))
            ->values()
            ->all();

        if (! $sections) {
            $body = $this->safeHtml->sanitize(
                $this->translated($post, 'body', $post->body, $locale),
            );

            if (PublicContentEligibility::hasMeaningfulText($body)) {
                $sections[] = [
                    'id' => null,
                    'title' => '',
                    'content' => $body,
                    'position' => 0,
                ];
            }
        }

        return [
            'title' => $title,
            'slug' => $post->slug,
            'excerpt' => $excerpt,
            'image' => $this->getImage($post),
            'reading_time' => $post->reading_time,
            'published_year' => $post->published_year,
            'published_at' => ($post->seo_published_at ?: $post->created_at)?->toAtomString(),
            'updated_at' => $post->updated_at?->toAtomString(),
            'meta' => [
                'title' => $metaTitle ?: $title,
                'description' => $metaDescription ?: $excerpt,
                'image' => $this->getImage($post),
                'keywords' => is_array($localizedKeywords)
                    ? array_values(array_filter($localizedKeywords, 'is_string'))
                    : ($post->seo_keywords ?? []),
                'noindex' => (bool) $post->noindex,
                'schema' => $post->schema,
                'author' => $post->seo_author,
            ],
            'faq' => $post->faq ?? [],
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
            'sections' => $sections,
            'related' => $this->getRelatedPosts($post, $locale),
        ];
    }

    private function getImage(Post $post): ?string
    {
        return $post->image;
    }

    private function getAuthorAvatar(Post $post): ?string
    {
        return $post->author?->avatar;
    }

    private function getRelatedPosts(Post $post, string $locale): array
    {
        return Post::query()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->publiclyVisible()
            ->latest()
            ->limit(12)
            ->with(['media', 'sections:id,post_id,content,translations'])
            ->get()
            ->filter(fn (Post $item): bool => PublicContentEligibility::post($item, $locale))
            ->take(3)
            ->map(fn (Post $item) => [
                'title' => $this->translated($item, 'title', $item->title, $locale),
                'slug' => $item->slug,
                'image' => $this->getImage($item),
            ])
            ->all();
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
