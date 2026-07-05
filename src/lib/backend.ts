import "server-only";

import { getServiceDetail } from "@/features/service-detail/data/services";
import type { ServiceDetail } from "@/features/service-detail/model/types";
import { projectDetails, type ProjectDetail } from "@/lib/projectDetails";
import {
    featuredProjects,
    projects,
    type FeaturedProject,
    type Project,
} from "@/lib/projects";
import { DEFAULT_SOCIAL_IMAGE } from "@/lib/seo";
import { services as serviceCards } from "@/lib/services";
import type { TeamMember } from "@/lib/team";
import { teamMembers } from "@/lib/team";

const apiBase = (
    process.env.BACKEND_API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    "http://localhost:8000/api"
).replace(/\/$/, "");

const apiOrigin = (() => {
    try {
        return new URL(apiBase).origin;
    } catch {
        return "";
    }
})();

export type BackendContent = {
    team?: TeamMember[];
    partners?: Array<{
        name: string;
        logo?: string;
        url?: string;
        category?: string;
    }>;
    testimonials?: Array<{
        quote: string;
        author: string;
        role?: string;
        company?: string;
        image?: string;
    }>;
    faqs?: Array<{ question: string; answer: string; context?: string }>;
    settings?: Record<string, unknown>;
};

async function fetchData<T>(path: string): Promise<T | undefined> {
    try {
        const response = await fetch(`${apiBase}${path}`, {
            next: { revalidate: 300 },
            signal: AbortSignal.timeout(3000),
        });

        if (!response.ok) return undefined;

        return ((await response.json()) as { data: T }).data;
    } catch {
        return undefined;
    }
}

export function resolveBackendAsset(
    path?: string | null,
    fallback = DEFAULT_SOCIAL_IMAGE,
) {
    if (!path) return fallback;
    if (path.startsWith("http") || path.startsWith("/")) return path;

    return apiOrigin ? `${apiOrigin}/storage/${path}` : fallback;
}

export function maybeBackendAsset(path?: string | null) {
    const resolved = resolveBackendAsset(path, "");
    return resolved || null;
}

function uniqueSlugs(slugs: Array<string | undefined>) {
    return [...new Set(slugs.filter((slug): slug is string => Boolean(slug)))];
}

export async function getBackendServices() {
    const remote = await fetchData<Array<ServiceDetail & { icon?: string }>>(
        "/services",
    );

    if (!remote?.length) return serviceCards;

    return remote.map((service) => ({
        slug: service.slug,
        title: service.name || service.title,
        description: service.description,
        icon: service.icon || "settings",
    }));
}

export async function getBackendServiceSlugs() {
    return uniqueSlugs((await getBackendServices()).map(({ slug }) => slug));
}

export async function getBackendService(
    slug: string,
): Promise<ServiceDetail | undefined> {
    const fallback = getServiceDetail(slug);
    const remote = await fetchData<ServiceDetail>(
        `/services/${encodeURIComponent(slug)}`,
    );

    if (!remote) return fallback;

    return {
        ...remote,
        heroImage: resolveBackendAsset(remote.heroImage),
        keywords: remote.keywords ?? [],
        highlights: remote.highlights ?? [],
        overview: remote.overview ?? {
            title: remote.title,
            paragraphs: [remote.description],
            stats: [],
        },
        benefits: remote.benefits ?? [],
        solutions: remote.solutions ?? [],
        industries: remote.industries ?? [],
        process: remote.process ?? [],
        faqs: remote.faqs ?? [],
        related: remote.related?.length
            ? remote.related
            : (fallback?.related ?? []),
    };
}

export async function getBackendProjects() {
    return (
        (await fetchData<
            Array<
                ProjectDetail & {
                    featured?: boolean;
                    category?: string;
                    technology?: string;
                    icon?: string;
                    accent?: "primary" | "secondary";
                    publishedAt?: string;
                }
            >
        >("/projects")) ?? []
    );
}

export async function getBackendFeaturedProjects(): Promise<FeaturedProject[]> {
    const remote = await fetchData<Array<ProjectDetail & { featured?: boolean }>>(
        "/projects?featured=1",
    );

    if (!remote?.length) return featuredProjects;

    return remote.map((item) => ({
        title: item.name,
        category: item.meta?.[0]?.value || "Case Study",
        image: resolveBackendAsset(item.image),
        imageAlt: item.imageAlt || item.name,
        specs: item.specs ?? [],
    }));
}

export async function getBackendProjectCards(): Promise<Project[]> {
    const remote = await getBackendProjects();

    if (!remote.length) return projects;

    return remote.map((item) => ({
        slug: item.slug,
        title: item.name,
        description: item.description,
        category: (item.category as Project["category"]) || "offices",
        icon: item.icon || "business",
        accent: item.accent || "primary",
        technology: item.technology || "SafeTech",
    }));
}

export async function getBackendProject(
    slug: string,
): Promise<ProjectDetail | undefined> {
    const fallback = projectDetails.find((project) => project.slug === slug);
    const remote = await fetchData<ProjectDetail>(
        `/projects/${encodeURIComponent(slug)}`,
    );

    if (!remote) return fallback;

    return {
        ...remote,
        image: resolveBackendAsset(remote.image),
        gallery: (remote.gallery ?? []).map((image) => ({
            ...image,
            src: resolveBackendAsset(image.src),
        })),
        meta: remote.meta ?? [],
        scope: remote.scope ?? [],
        specs: remote.specs ?? [],
        challenges: remote.challenges ?? [],
        solutions: remote.solutions ?? [],
        process: remote.process ?? [],
        results: remote.results ?? [],
        testimonial: remote.testimonial ?? { quote: "", author: "", role: "" },
        related: remote.related ?? [],
    };
}

export async function getBackendProjectSlugs() {
    return uniqueSlugs(
        (await getBackendProjectCards()).map(({ slug }) => slug),
    );
}

export async function getBackendTeam(): Promise<TeamMember[]> {
    const content = await getBackendContent();

    if (!content?.team?.length) return teamMembers;

    return content.team.map((member) => ({
        ...member,
        image: resolveBackendAsset(member.image, "/team-avatar.svg"),
        socials: member.socials ?? {},
    }));
}

export async function getBackendContent(): Promise<BackendContent> {
    return (await fetchData<BackendContent>("/content")) ?? {};
}
