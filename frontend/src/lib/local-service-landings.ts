import "server-only";

import { maybeBackendAsset } from "@/lib/backend";
import { getServerApiBase } from "@/lib/backend-api";
import { getCurrentLocale } from "@/lib/locale-server";

export type LocalServiceLandingProject = {
    slug: string;
    title: string;
    description?: string | null;
    image?: string | null;
};

export type LocalServiceLandingProjectReference = {
    slug: string;
};

export type LocalServiceLandingSummary = {
    id: number;
    translationAvailable?: boolean;
    locationSlug: string;
    locationName: string;
    title: string;
    service: {
        slug: string;
        name: string;
        title: string;
    };
    projects: LocalServiceLandingProjectReference[];
    seo?: {
        title?: string;
        noindex?: boolean;
    };
    updated_at?: string;
};

export type LocalServiceLanding = Omit<
    LocalServiceLandingSummary,
    "projects" | "seo" | "service"
> & {
    eyebrow?: string | null;
    excerpt?: string | null;
    content: string;
    benefits: Array<{
        title?: string;
        description?: string;
    }>;
    faqs: Array<{
        question?: string;
        answer?: string;
    }>;
    ctaTitle?: string | null;
    ctaText?: string | null;
    primaryKeyword?: string | null;
    keywords: string[];
    service: LocalServiceLandingSummary["service"] & {
        heroImage?: string | null;
    };
    projects: LocalServiceLandingProject[];
    seo?: LocalServiceLandingSummary["seo"] & {
        description?: string;
        keywords?: string[];
        image?: string | null;
        canonical?: string;
        schemaType?: string;
        og?: { title?: string; description?: string };
        schema?: Record<string, unknown> | Array<Record<string, unknown>>;
    };
};

const serverApiBase = getServerApiBase();

function apiPath(path: string, params: Record<string, string | undefined> = {}) {
    const query = new URLSearchParams();

    for (const [key, value] of Object.entries(params)) {
        if (value) query.set(key, value);
    }

    return query.size ? `${path}?${query.toString()}` : path;
}

async function fetchData<T>(path: string, fresh = false): Promise<T | undefined> {
    const url = `${serverApiBase}${path}`;

    for (let attempt = 0; attempt < 2; attempt += 1) {
        try {
            const response = await fetch(url, {
                ...(fresh ? { cache: "no-store" as const } : { next: { revalidate: 300, tags: ["cms"] } }),
                signal: AbortSignal.timeout(7000),
            });

            if (response.status === 404) return undefined;

            if (!response.ok) {
                if ((response.status === 429 || response.status >= 500) && attempt === 0) {
                    continue;
                }

                return undefined;
            }

            const payload = (await response.json()) as { data?: T };

            if (payload.data !== undefined) return payload.data;

            if (attempt === 0) continue;
        } catch {
            if (attempt === 0) continue;
        }
    }

    return undefined;
}

function normalizeLanding(landing: LocalServiceLanding): LocalServiceLanding {
    return {
        ...landing,
        service: {
            ...landing.service,
            heroImage: maybeBackendAsset(landing.service.heroImage),
        },
        projects: (landing.projects ?? []).map((project) => ({
            ...project,
            image: maybeBackendAsset(project.image),
        })),
        seo: landing.seo
            ? {
                  ...landing.seo,
                  image: maybeBackendAsset(landing.seo.image),
              }
            : undefined,
        benefits: landing.benefits ?? [],
        faqs: landing.faqs ?? [],
        keywords: landing.keywords ?? [],
    };
}

export async function getLocalServiceLanding(
    serviceSlug: string,
    locationSlug: string,
): Promise<LocalServiceLanding | undefined> {
    const locale = await getCurrentLocale();
    const landing = await fetchData<LocalServiceLanding>(
        apiPath(
            `/local-service-landings/${encodeURIComponent(serviceSlug)}/${encodeURIComponent(locationSlug)}`,
            { locale },
        ),
        true,
    );

    return landing && landing.translationAvailable !== false
        ? normalizeLanding(landing)
        : undefined;
}

export async function getLocalServiceLandings(
    serviceSlug?: string,
): Promise<LocalServiceLandingSummary[]> {
    const locale = await getCurrentLocale();
    const landings = await fetchData<LocalServiceLandingSummary[]>(
        apiPath("/local-service-landings", {
            locale,
            service: serviceSlug,
            view: "summary",
        }),
    );

    return (landings ?? [])
        .filter((landing) => landing.translationAvailable !== false)
        .map((landing) => ({
            ...landing,
            projects: landing.projects ?? [],
        }));
}
