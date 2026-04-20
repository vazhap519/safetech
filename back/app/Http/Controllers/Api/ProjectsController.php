<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\SocialLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProjectsController extends Controller
{
    /* =========================
       🔥 PROJECT LIST
    ========================= */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 6); // 🔥 dynamic pagination

        $query = Project::published()
            ->select('id', 'title', 'slug', 'excerpt', 'category_id')
            ->with([
                'category:id,name,slug',
                'media'
            ]);

        /* =========================
           🔥 CATEGORY FILTER
        ========================= */
        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        /* =========================
           🔥 ORDER
        ========================= */
        $projects = $query
            ->orderBy('sort_order')
            ->latest()
            ->paginate($perPage)
            ->appends($request->query()); // 🔥 keeps query params

        /* =========================
           🔥 TRANSFORM (clean way)
        ========================= */
        $projects->getCollection()->transform(function ($project) {
            return [
                'title' => $project->title,
                'slug' => $project->slug,
                'excerpt' => $project->excerpt,

                /* 🔥 IMAGE */
                'image' => $project->thumb_url,

                /* 🔥 CATEGORY */
                'category' => [
                    'name' => $project->category->name,
                    'slug' => $project->category->slug,
                ],
            ];
        });

        return response()->json([
            'data' => $projects->items(),

            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],

            /* 🔥 OPTIONAL (future use) */
            'links' => [
                'next' => $projects->nextPageUrl(),
                'prev' => $projects->previousPageUrl(),
            ]
        ]);
    }

    /* =========================
       🔥 CATEGORIES LIST
    ========================= */
    public function categories()
    {
        $columns = array_values(array_filter([
            'id',
            'name',
            'slug',
            'color',
            'icon',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'intro_text',
            'faq',
            'schema',
            'noindex',
        ], fn ($column) => Schema::hasColumn('project_categories', $column)));

        $categories = ProjectCategory::query()
            ->select($columns)
            ->has('projects') // 🔥 only categories with projects
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    public function show($slug)
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with([
                'category:id,name,slug',
                'media'
            ])
            ->first();

        if (!$project) {
            return response()->json([
                'error' => 'Project not found'
            ], 404);
        }

        $url = SocialLinks::frontendUrl("/projects/{$project->slug}");
        $seo = $project->seo ?? [];
        $settings = settings();
        $shareButtons = SocialLinks::shareButtons($settings?->share_buttons ?? []);

        return response()->json([
            'data' => [
                'title' => $project->title,
                'slug' => $project->slug,
                'excerpt' => $project->excerpt,
                'content' => $project->content,

                /* 🔥 MEDIA */
                'image' => $project->cover_url,
                'gallery' => $project->gallery_urls,

                /* 🔥 VIDEO */
                'video_url' => $project->video_url,

                /* 🔥 CATEGORY */
                'category' => [
                    'name' => $project->category->name,
                    'slug' => $project->category->slug,
                ],

                /* 🔥 SEO */
                'seo' => [
                    ...$seo,
                    'canonical' => data_get($seo, 'canonical', $url),
                    'noindex' => (bool) data_get($seo, 'noindex', false),
                    'image' => data_get($seo, 'image', $project->cover_url),
                ],

                /* 🔥 DATE */
                'published_at' => $project->published_at,
            ],
            'share' => [
                'title' => $settings?->share_title ?? '',
                'share_title' => $settings?->share_title ?? '',
                'url' => $url,
                'buttons' => $shareButtons,
                'share_buttons' => $shareButtons,
            ],
        ]);
    }

    public function related($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        $projects = Project::published()
            ->where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->with(['category:id,name,slug', 'media'])
            ->latest()
            ->take(3) // 🔥 შეგიძლია 4 ან 6
            ->get();

        $data = $projects->map(function ($project) {
            return [
                'title' => $project->title,
                'slug' => $project->slug,
                'excerpt' => $project->excerpt,
                'image' => $project->thumb_url,

                'category' => [
                    'name' => $project->category->name,
                    'slug' => $project->category->slug,
                ],
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }
}
