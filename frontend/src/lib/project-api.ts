import "server-only";

import { getPublicApiOrigin, getServerApiBase } from "@/lib/backend-api";
import { getCurrentLocale } from "@/lib/locale-server";
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

async function fetchProjectData<T>(path: string): Promise<T | undefined> {
    try {
        const response = await fetch(`${serverApiBase}${path}`, {
            next: { revalidate: 300, tags: ["cms"] },
            signal: AbortSignal.timeout(3000),
        });

        if (!response.ok) return undefined;

        return ((await response.json()) as { data: T }).data;
    } catch {
        return undefined;
    }
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

export async function getLocalizedProjects(category?: string): Promise<ApiProject[]> {
    const locale = await getCurrentLocale();

    return (
        (await fetchProjectData<ApiProject[]>(
            apiPath("/projects", { locale, category }),
        )) ?? []
    );
}

export async function getLocalizedProjectCards(category?: string): Promise<Project[]> {
    const projects = await getLocalizedProjects(category);

    return projects.map((project) => ({
        slug: project.slug,
        title: project.name || project.title || project.slug,
        description: project.description || "",
        category: project.category || "",
        icon: project.icon || "business",
        accent: project.accent || "primary",
        technology: project.technology || "",
        videoUrl: videoUrl(project),
    }));
}

export async function getLocalizedFeaturedProjects(): Promise<FeaturedProject[]> {
    const locale = await getCurrentLocale();
    const projects =
        (await fetchProjectData<ApiProject[]>(
            apiPath("/projects", { locale, featured: 1 }),
        )) ?? [];

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
): Promise<ProjectDetail | undefined> {
    const locale = await getCurrentLocale();
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
