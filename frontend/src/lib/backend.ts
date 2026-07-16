import "server-only";

import type { ServiceDetail } from "@/features/service-detail/model/types";
import { getPublicApiOrigin, getServerApiBase } from "@/lib/backend-api";
import {
    localizeLeadFormConfig,
    type ContactServiceOption,
    type RawLeadFormConfig,
} from "@/lib/lead-form";
import { getCurrentLocale } from "@/lib/locale-server";
import type { Locale } from "@/lib/locales";
import type { ProjectDetail } from "@/lib/projectDetails";
import type { FeaturedProject, Project } from "@/lib/projects";
import { DEFAULT_SOCIAL_IMAGE } from "@/lib/seo";
import type { TeamMember } from "@/lib/team";
import {
    buildTranslationMap,
    createTranslator,
    type TranslationMap,
} from "@/lib/translations";

const serverApiBase = getServerApiBase();
const publicApiOrigin = getPublicApiOrigin();

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

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

async function fetchData<T>(path: string): Promise<T | undefined> {
    try {
        const response = await fetch(`${serverApiBase}${path}`, {
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
    if (path.startsWith("http")) return path;

    if (path.startsWith("/storage") || path.startsWith("/uploads")) {
        return publicApiOrigin ? `${publicApiOrigin}${path}` : path;
    }

    if (path.startsWith("/")) return path;

    return publicApiOrigin ? `${publicApiOrigin}/storage/${path}` : fallback;
}

export function maybeBackendAsset(path?: string | null) {
    const resolved = resolveBackendAsset(path, "");
    return resolved || null;
}

function uniqueSlugs(slugs: Array<string | undefined>) {
    return [...new Set(slugs.filter((slug): slug is string => Boolean(slug)))];
}

async function getTranslationContext() {
    const [content, locale] = await Promise.all([
        getBackendContent(),
        getCurrentLocale(),
    ]);
    const settings = isRecord(content.settings) ? content.settings : {};

    return {
        locale: locale satisfies Locale,
        translations: buildTranslationMap(settings.translations) satisfies TranslationMap,
    };
}

function localizeStringArray(
    values: string[],
    prefix: string,
    t: ReturnType<typeof createTranslator>,
) {
    return values.map((value, index) => t(`${prefix}.${index}`, value));
}

function localizeServiceDetail(
    service: ServiceDetail,
    locale: Locale,
    translations: TranslationMap,
) {
    const t = createTranslator(translations, locale);
    const prefix = `service.${service.slug}`;

    return {
        ...service,
        name: t(`${prefix}.name`, service.name),
        eyebrow: t(`${prefix}.eyebrow`, service.eyebrow),
        title: t(`${prefix}.title`, service.title),
        description: t(`${prefix}.description`, service.description),
        seoDescription: t(`${prefix}.seoDescription`, service.seoDescription),
        keywords: localizeStringArray(service.keywords, `${prefix}.keyword`, t),
        highlights: localizeStringArray(
            service.highlights,
            `${prefix}.highlight`,
            t,
        ),
        overview: {
            title: t(`${prefix}.overview.title`, service.overview.title),
            paragraphs: service.overview.paragraphs.map((paragraph, index) =>
                t(`${prefix}.overview.paragraph.${index}`, paragraph),
            ),
            stats: service.overview.stats.map((stat, index) => ({
                value: t(`${prefix}.overview.stat.${index}.value`, stat.value),
                label: t(`${prefix}.overview.stat.${index}.label`, stat.label),
            })),
        },
        benefits: service.benefits.map((benefit, index) => ({
            ...benefit,
            title: t(`${prefix}.benefit.${index}.title`, benefit.title),
            description: t(
                `${prefix}.benefit.${index}.description`,
                benefit.description,
            ),
        })),
        solutions: service.solutions.map((solution, index) => ({
            ...solution,
            title: t(`${prefix}.solution.${index}.title`, solution.title),
            description: t(
                `${prefix}.solution.${index}.description`,
                solution.description,
            ),
        })),
        industries: localizeStringArray(
            service.industries,
            `${prefix}.industry`,
            t,
        ),
        process: service.process.map((step, index) => ({
            title: t(`${prefix}.process.${index}.title`, step.title),
            description: t(
                `${prefix}.process.${index}.description`,
                step.description,
            ),
        })),
        faqs: service.faqs.map((faq, index) => ({
            question: t(`${prefix}.faq.${index}.question`, faq.question),
            answer: t(`${prefix}.faq.${index}.answer`, faq.answer),
        })),
        related: service.related.map((related, index) => ({
            ...related,
            title: t(`${prefix}.related.${index}.title`, related.title),
            description: t(
                `${prefix}.related.${index}.description`,
                related.description,
            ),
        })),
    } satisfies ServiceDetail;
}

function localizeProjectDetail(
    project: ProjectDetail,
    locale: Locale,
    translations: TranslationMap,
) {
    const t = createTranslator(translations, locale);
    const prefix = `project.${project.slug}`;

    return {
        ...project,
        name: t(`${prefix}.name`, project.name),
        title: t(`${prefix}.title`, project.title),
        description: t(`${prefix}.description`, project.description),
        seoDescription: t(
            `${prefix}.seoDescription`,
            project.seoDescription,
        ),
        imageAlt: t(`${prefix}.imageAlt`, project.imageAlt),
        meta: project.meta.map((item, index) => ({
            label: t(`${prefix}.meta.${index}.label`, item.label),
            value: t(`${prefix}.meta.${index}.value`, item.value),
        })),
        scope: project.scope.map((item, index) => ({
            label: t(`${prefix}.scope.${index}.label`, item.label),
            value: t(`${prefix}.scope.${index}.value`, item.value),
        })),
        specs: project.specs.map((item, index) => ({
            label: t(`${prefix}.spec.${index}.label`, item.label),
            value: t(`${prefix}.spec.${index}.value`, item.value),
        })),
        challenges: project.challenges.map((item, index) => ({
            ...item,
            title: t(`${prefix}.challenge.${index}.title`, item.title),
            description: t(
                `${prefix}.challenge.${index}.description`,
                item.description,
            ),
        })),
        solutions: project.solutions.map((item, index) => ({
            ...item,
            title: t(`${prefix}.solution.${index}.title`, item.title),
            description: t(
                `${prefix}.solution.${index}.description`,
                item.description,
            ),
        })),
        process: project.process.map((step, index) => ({
            title: t(`${prefix}.process.${index}.title`, step.title),
            description: t(
                `${prefix}.process.${index}.description`,
                step.description,
            ),
        })),
        gallery: project.gallery.map((item, index) => ({
            ...item,
            alt: t(`${prefix}.gallery.${index}.alt`, item.alt),
        })),
        results: project.results.map((item, index) => ({
            ...item,
            value: t(`${prefix}.result.${index}.value`, item.value),
            title: t(`${prefix}.result.${index}.title`, item.title),
            description: t(
                `${prefix}.result.${index}.description`,
                item.description,
            ),
        })),
        testimonial: {
            quote: t(`${prefix}.testimonial.quote`, project.testimonial.quote),
            author: t(
                `${prefix}.testimonial.author`,
                project.testimonial.author,
            ),
            role: t(`${prefix}.testimonial.role`, project.testimonial.role),
        },
        related: project.related.map((item, index) => ({
            ...item,
            title: t(`${prefix}.related.${index}.title`, item.title),
            category: t(`${prefix}.related.${index}.category`, item.category),
            imageAlt: t(`${prefix}.related.${index}.imageAlt`, item.imageAlt),
        })),
    } satisfies ProjectDetail;
}

export async function getBackendServices() {
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        fetchData<Array<ServiceDetail & { icon?: string }>>("/services"),
    ]);
    const t = createTranslator(translations, locale);

    if (!remote?.length) return [];

    return remote.map((service) => ({
        slug: service.slug,
        title: t(
            `service.${service.slug}.card.title`,
            service.name || service.title,
        ),
        description: t(
            `service.${service.slug}.card.description`,
            service.description,
        ),
        icon: service.icon || "settings",
    }));
}

export async function getBackendContactServices(): Promise<
    ContactServiceOption[]
> {
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        fetchData<
            Array<{
                slug: string;
                name?: string;
                title?: string;
                leadForm?: RawLeadFormConfig | null;
            }>
        >("/services"),
    ]);
    const t = createTranslator(translations, locale);

    if (!remote?.length) return [];

    return remote.map((service) => ({
        slug: service.slug,
        label: t(
            `service.${service.slug}.name`,
            service.name || service.title || service.slug,
        ),
        leadForm: localizeLeadFormConfig(service.leadForm ?? null, locale),
    }));
}

export async function getBackendServiceSlugs() {
    const remote = await fetchData<Array<{ slug: string }>>("/services");

    return uniqueSlugs(remote?.map(({ slug }) => slug) ?? []);
}

export async function getBackendService(
    slug: string,
): Promise<ServiceDetail | undefined> {
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        fetchData<ServiceDetail>(`/services/${encodeURIComponent(slug)}`),
    ]);

    if (!remote) return undefined;

    return localizeServiceDetail(
        {
            ...remote,
            heroImage: resolveBackendAsset(remote.heroImage),
            keywords: remote.keywords ?? [],
            highlights: remote.highlights ?? [],
            overview: remote.overview ?? {
                title: remote.title || remote.name,
                paragraphs: remote.description ? [remote.description] : [],
                stats: [],
            },
            benefits: remote.benefits ?? [],
            solutions: remote.solutions ?? [],
            industries: remote.industries ?? [],
            process: remote.process ?? [],
            faqs: remote.faqs ?? [],
            related: remote.related ?? [],
        },
        locale,
        translations,
    );
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
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        fetchData<Array<ProjectDetail & { featured?: boolean }>>(
            "/projects?featured=1",
        ),
    ]);
    const t = createTranslator(translations, locale);

    if (!remote?.length) return [];

    return remote.map((item) => ({
        title: t(
            `project.${item.slug}.featured.title`,
            item.name || item.title || item.slug,
        ),
        category: t(
            `project.${item.slug}.featured.category`,
            item.meta?.[0]?.value || item.name || item.title || item.slug,
        ),
        image: resolveBackendAsset(item.image),
        imageAlt: t(
            `project.${item.slug}.featured.imageAlt`,
            item.imageAlt || item.name || item.title || item.slug,
        ),
        specs: (item.specs ?? []).map((spec, index) => ({
            value: t(
                `project.${item.slug}.featured.spec.${index}.value`,
                spec.value,
            ),
            label: t(
                `project.${item.slug}.featured.spec.${index}.label`,
                spec.label,
            ),
        })),
    }));
}

export async function getBackendProjectCards(): Promise<Project[]> {
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        getBackendProjects(),
    ]);
    const t = createTranslator(translations, locale);

    if (!remote.length) return [];

    return remote.map((item) => ({
        slug: item.slug,
        title: t(
            `project.${item.slug}.card.title`,
            item.name || item.title || item.slug,
        ),
        description: t(
            `project.${item.slug}.card.description`,
            item.description,
        ),
        category: (item.category as Project["category"]) || "offices",
        icon: item.icon || "business",
        accent: item.accent || "primary",
        technology: t(
            `project.${item.slug}.technology`,
            item.technology || "SafeTech",
        ),
    }));
}

export async function getBackendProject(
    slug: string,
): Promise<ProjectDetail | undefined> {
    const [{ locale, translations }, remote] = await Promise.all([
        getTranslationContext(),
        fetchData<ProjectDetail>(`/projects/${encodeURIComponent(slug)}`),
    ]);

    if (!remote) return undefined;

    return localizeProjectDetail(
        {
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
        },
        locale,
        translations,
    );
}

export async function getBackendProjectSlugs() {
    const remote = await fetchData<Array<{ slug: string }>>("/projects");

    return uniqueSlugs(remote?.map(({ slug }) => slug) ?? []);
}

export async function getBackendTeam(): Promise<TeamMember[]> {
    const [content, locale] = await Promise.all([
        getBackendContent(),
        getCurrentLocale(),
    ]);
    const settings = isRecord(content.settings) ? content.settings : {};
    const translations = buildTranslationMap(settings.translations);
    const t = createTranslator(translations, locale);

    if (!content?.team?.length) return [];

    return content.team.map((member) => ({
        ...member,
        firstName: t(`team.${member.id}.firstName`, member.firstName),
        lastName: t(`team.${member.id}.lastName`, member.lastName),
        position: t(`team.${member.id}.position`, member.position),
        image: resolveBackendAsset(member.image, "/team-avatar.svg"),
        socials: member.socials ?? {},
    }));
}

export async function getBackendContent(): Promise<BackendContent> {
    return (await fetchData<BackendContent>("/content")) ?? {};
}
