import JsonLd from "@/components/seo/JsonLd";
import type { LocalServiceLandingSummary } from "@/lib/local-service-landings";
import { getLanguageTag } from "@/lib/locales";
import type { ProjectDetail } from "@/lib/projectDetails";
import { projectVideoPath } from "@/lib/project-video";
import {
    absoluteLocalizedUrl,
    absoluteSiteUrl,
    DEFAULT_SOCIAL_IMAGE,
} from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import {
    buildBreadcrumbSchema,
    type StructuredDataValue,
} from "@/lib/structured-data";
import { createTranslator } from "@/lib/translations";
import { getYouTubeEmbedUrl } from "@/lib/youtube";

function withoutEmbeddedVideoSchema(data: StructuredDataValue): StructuredDataValue {
    const enrich = (value: unknown): unknown => {
        if (Array.isArray(value)) return value.map(enrich).filter((item) => item !== undefined);
        if (!value || typeof value !== "object") return value;

        const normalized = Object.fromEntries(
            Object.entries(value)
                .map(([key, nestedValue]) => [key, enrich(nestedValue)])
                .filter(([, nestedValue]) => nestedValue !== undefined),
        );
        const type = normalized["@type"];
        const isVideoObject =
            type === "VideoObject" ||
            (Array.isArray(type) && type.includes("VideoObject"));

        // Playback and its VideoObject now belong to the dedicated watch page.
        if (isVideoObject) return undefined;

        return normalized;
    };

    return (enrich(data) ?? []) as StructuredDataValue;
}

function structuredDataItems(data: StructuredDataValue) {
    return Array.isArray(data) ? data : [data];
}

export default async function ProjectDetailSchema({
    project,
    localLandings = [],
}: {
    project: ProjectDetail;
    localLandings?: LocalServiceLandingSummary[];
}) {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const url = absoluteLocalizedUrl(`/projects/${project.slug}`, locale);
    const projectId = `${url}#project`;
    const videoEmbedUrl = getYouTubeEmbedUrl(project.videoUrl);
    const description = project.seoDescription || project.description;
    const projectImage = project.image || branding.defaultImage || DEFAULT_SOCIAL_IMAGE;
    const organizationLogo =
        branding.logo || branding.footerLogo || branding.defaultImage || DEFAULT_SOCIAL_IMAGE;
    const serviceTopics = localLandings.map((landing) => ({
        "@type": "Service",
        name: landing.service.name || landing.service.title,
        url: absoluteLocalizedUrl(
            `/services/${landing.service.slug}/${landing.locationSlug}`,
            locale,
        ),
        areaServed: { "@type": "City", name: landing.locationName },
    }));
    const projectTopics = [
        ...(project.objectType ? [{ "@type": "Thing", name: project.objectType }] : []),
        ...(project.equipment ?? [])
            .filter((item) => item.name)
            .map((item) => ({
                "@type": "Product",
                name: [item.name, item.model].filter(Boolean).join(" "),
                ...(item.quantity ? { description: `${item.quantity} — ${item.name}` } : {}),
            })),
    ];
    const about = [...serviceTopics, ...projectTopics];
    const graph: Record<string, unknown>[] = [
        {
            "@type": project.seo?.schemaType || "Article",
            "@id": projectId,
            name: project.title || project.name,
            description,
            image: absoluteSiteUrl(projectImage),
            url,
            mainEntityOfPage: url,
            ...(about.length ? { about } : {}),
            ...(project.city
                ? { spatialCoverage: { "@type": "Place", name: project.city } }
                : {}),
            ...(project.publishedAt ? { datePublished: project.publishedAt } : {}),
            ...(project.updated_at ? { dateModified: project.updated_at } : {}),
            ...(videoEmbedUrl ? { subjectOf: {
                "@type": "WebPage",
                url: absoluteLocalizedUrl(projectVideoPath(project.slug), locale),
            } } : {}),
            creator: {
                "@type": "Organization",
                name: branding.siteName,
                url: absoluteLocalizedUrl("/", locale),
                logo: absoluteSiteUrl(organizationLogo),
            },
            inLanguage: getLanguageTag(locale),
        },
        buildBreadcrumbSchema([
            {
                name: t("nav.home", { ka: "მთავარი", en: "Home", ru: "Главная" }),
                url: absoluteLocalizedUrl("/", locale),
            },
            {
                name: t("nav.projects", {
                    ka: "პროექტები",
                    en: "Projects",
                    ru: "Проекты",
                }),
                url: absoluteLocalizedUrl("/projects", locale),
            },
            { name: project.title || project.name, url },
        ]),
    ];
    const schema = { "@context": "https://schema.org", "@graph": graph };

    if (project.seo?.schema) {
        const customSchema = withoutEmbeddedVideoSchema(project.seo.schema);
        return <JsonLd data={[schema, ...structuredDataItems(customSchema)]} />;
    }

    return <JsonLd data={schema} />;
}
