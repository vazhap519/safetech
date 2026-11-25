import "server-only";

import { getPublicApiOrigin, getServerApiBase } from "@/lib/backend-api";
import { getCurrentLocale } from "@/lib/locale-server";
import { normalizeLocale } from "@/lib/locales";
import type { ProjectDetail } from "@/lib/projectDetails";
import type { FeaturedProject, Project } from "@/lib/projects";

const serverApiBase = getServerApiBase();
const publicApiOrigin = getPublicApiOrigin();

type ApiProject = ProjectDetail & {
    category?: string;
    categoryName?: string;
    technology?: string;
    icon?: string;
    accent?: "primary" | "secondary";
    featured?: boolean;
    video_url?: string | null;
};

export type ProjectPage = {
    projects: Project[];
    currentPage: number;
    lastPage: number;
    total: number;
};

async function fetchProjectData<T>(path: string): Promise<T | undefined> {
    for (let attempt = 0; attempt < 2; attempt += 1) {
        try {
            const response = await fetch(`${serverApiBase}${path}`, {
                // Successful CMS revalidation remains immediate. A short TTL
                // bounds staleness when its queue job fails while allowing
                // metadata and page rendering to share the same API response.
                next: {
                    revalidate: 15,
                    tags: ["cms", "projects"],
                },
                signal: AbortSignal.timeout(7000),
            });

            if (response.ok) {
                return ((await response.json()) as { data: T }).data;
            }

            if (response.status !== 429 && response.status < 500) {
                return undefined;
            }
        } catch {
            // Retry one transient API timeout/network failure. Project pages
            // must not become false 404/500 responses during brief load spikes.
        }
    }

    return undefined;
}

async function fetchProjectPageData(path: string): Promise<{
    data: ApiProject[];
    meta?: {
        current_page?: number;
        last_page?: number;
        total?: number;
    };
} | undefined> {
    for (let attempt = 0; attempt < 2; attempt += 1) {
        try {
            const response = await fetch(`${serverApiBase}${path}`, {
                next: { revalidate: 15, tags: ["cms", "projects"] },
                signal: AbortSignal.timeout(7000),
            });

            if (response.ok) return await response.json();
            if (response.status !== 429 && response.status < 500) return undefined;
        } catch {
            // Retry one transient API/network failure.
        }
    }

    return undefined;
}

function toProjectCard(project: ApiProject): Project {
    return {
        slug: project.slug,
        title: project.name || project.title || project.slug,
        description: project.description || "",
        category: project.category || "",
        icon: project.icon || "business",
        accent: project.accent || "primary",
        technology: project.technology || "",
        videoUrl: videoUrl(project),
    };
}

function asset(path?: string | null): string {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    if (path.startsWith("/storage") || path.startsWith("/uploads")) {
        return publicApiOrigin ? `${publicApiOrigin}${path}` : path;
    }
    if (path.startsWith("/")) return path;

    return publicApiOrigin ? `${publicApiOrigin}/storage/${path}` : path;
}

function videoUrl(project: ApiProject): string {
    return project.videoUrl || project.video_url || "";
}

function apiPath(
    path: string,
    params: Record<string, string | number | boolean | undefined> = {},
): string {
    const query = new URLSearchParams();

    for (const [key, value] of Object.entries(params)) {
        if (value === undefined || value === "") continue;
        query.set(key, String(value));
    }

    const suffix = query.toString();
    return suffix ? `${path}?${suffix}` : path;
}

async function resolveProjectLocale(locale?: string) {
    if (locale) {
        return normalizeLocale(locale);
    }

    return getCurrentLocale();
}

export async function getLocalizedProjects(
    category?: string,
    localeOverride?: string,
): Promise<ApiProject[]> {
    const locale = await resolveProjectLocale(localeOverride);

    return (
        (await fetchProjectData<ApiProject[]>(
            apiPath("/projects", { locale, category, view: "summary" }),
        )) ?? []
    );
}

export async function getLocalizedProjectCards(
    category?: string,
    localeOverride?: string,
): Promise<Project[]> {
    const projects = await getLocalizedProjects(category, localeOverride);

    return projects.map(toProjectCard);
}

export async function getLocalizedProjectPage(
    category?: string,
    page = 1,
    perPage = 9,
    localeOverride?: string,
): Promise<ProjectPage> {
    const locale = await resolveProjectLocale(localeOverride);
    const response = await fetchProjectPageData(
        apiPath("/projects", {
            locale,
            category,
            page,
            per_page: perPage,
            view: "summary",
        }),
    );

    return {
        projects: (response?.data ?? []).map(toProjectCard),
        currentPage: response?.meta?.current_page ?? 1,
        lastPage: response?.meta?.last_page ?? 1,
        total: response?.meta?.total ?? response?.data?.length ?? 0,
    };
}

export async function getLocalizedFeaturedProjects(
    localeOverride?: string,
    fallbackToPublished = true,
    limit?: number,
): Promise<FeaturedProject[]> {
    const locale = await resolveProjectLocale(localeOverride);
    const featuredProjects =
        (await fetchProjectData<ApiProject[]>(
            apiPath("/projects", {
                locale,
                featured: 1,
                per_page: limit,
                view: "summary",
            }),
        )) ?? [];
    const projects =
        featuredProjects.length || !fallbackToPublished
            ? featuredProjects
            : ((await fetchProjectData<ApiProject[]>(
                  apiPath("/projects", {
                      locale,
                      per_page: limit,
                      view: "summary",
                  }),
              )) ?? []);

    return projects.map((project) => ({
        slug: project.slug,
        title: project.name || project.title || project.slug,
        category:
            project.categoryName ||
            project.meta?.[0]?.value ||
            project.name ||
            project.title ||
            project.slug,
        image: asset(project.image),
        imageAlt: project.imageAlt || project.name || project.title || project.slug,
        videoUrl: videoUrl(project),
        specs: project.specs ?? [],
    }));
}

export async function getLocalizedProject(
    slug: string,
    localeOverride?: string,
): Promise<ProjectDetail | undefined> {
    const locale = await resolveProjectLocale(localeOverride);
    const project = await fetchProjectData<ApiProject>(
        apiPath(`/projects/${encodeURIComponent(slug)}`, { locale }),
    );

    if (!project) return undefined;

    return {
        ...project,
        image: asset(project.image),
        videoUrl: videoUrl(project),
        gallery: (project.gallery ?? []).map((item) => ({
            ...item,
            src: asset(item.src),
        })),
        meta: project.meta ?? [],
        scope: project.scope ?? [],
        specs: project.specs ?? [],
        challenges: project.challenges ?? [],
        solutions: project.solutions ?? [],
        process: project.process ?? [],
        results: project.results ?? [],
        related: (project.related ?? []).map((item) => ({
            ...item,
            image: asset(item.image),
        })),
    };
}
