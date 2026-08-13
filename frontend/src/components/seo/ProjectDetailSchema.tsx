import type { ProjectDetail } from "@/lib/projectDetails";
import JsonLd from "@/components/seo/JsonLd";
import { getLanguageTag } from "@/lib/locales";
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
import { getYouTubeEmbedUrl, getYouTubeWatchUrl } from "@/lib/youtube";

function ensureVideoUploadDate(
    data: StructuredDataValue,
    uploadDate: string,
): StructuredDataValue {
    const enrich = (value: unknown): unknown => {
        if (Array.isArray(value)) {
            return value.map(enrich);
        }

        if (!value || typeof value !== "object") {
            return value;
        }

        const normalized = Object.fromEntries(
            Object.entries(value).map(([key, nestedValue]) => [
                key,
                enrich(nestedValue),
            ]),
        );
        const type = normalized["@type"];
        const isVideoObject =
            type === "VideoObject" ||
            (Array.isArray(type) && type.includes("VideoObject"));

        if (isVideoObject && !normalized.uploadDate && uploadDate) {
            normalized.uploadDate = uploadDate;
        }

        return normalized;
    };

    return enrich(data) as StructuredDataValue;
}

function structuredDataItems(data: StructuredDataValue) {
    return Array.isArray(data) ? data : [data];
}

export default async function ProjectDetailSchema({
    project,
}: {
    project: ProjectDetail;
}) {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const url = absoluteLocalizedUrl(`/projects/${project.slug}`, locale);
    const videoEmbedUrl = getYouTubeEmbedUrl(project.videoUrl);
    const videoWatchUrl = getYouTubeWatchUrl(project.videoUrl);
    const videoUploadDate = project.publishedAt || project.updated_at || "";
    const description = project.seoDescription || project.description;
    const projectImage =
        project.image || branding.defaultImage || DEFAULT_SOCIAL_IMAGE;
    const organizationLogo =
        branding.logo ||
        branding.footerLogo ||
        branding.defaultImage ||
        DEFAULT_SOCIAL_IMAGE;
    const schema = {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "CreativeWork",
                "@id": `${url}#project`,
                name: project.title || project.name,
                description,
                image: absoluteSiteUrl(projectImage),
                url,
                mainEntityOfPage: url,
                ...(project.publishedAt
                    ? { datePublished: project.publishedAt }
                    : {}),
                ...(project.updated_at
                    ? { dateModified: project.updated_at }
                    : {}),
                ...(videoWatchUrl && videoUploadDate
                    ? {
                          video: {
                              "@type": "VideoObject",
                              name: project.title || project.name,
                              description,
                              thumbnailUrl: absoluteSiteUrl(projectImage),
                              uploadDate: videoUploadDate,
                              url: videoWatchUrl,
                              ...(videoEmbedUrl
                                  ? { embedUrl: videoEmbedUrl }
                                  : {}),
                          },
                      }
                    : {}),
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
                    name: t("nav.home", {
                            ka: "მთავარი",
                            en: "Home",
                            ru: "Главная",
                        }),
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
                {
                    name: project.title || project.name,
                    url,
                },
            ]),
        ],
    };

    if (project.seo?.schema) {
        const customSchema = ensureVideoUploadDate(
            project.seo.schema,
            videoUploadDate,
        );

        return (
            <JsonLd data={[schema, ...structuredDataItems(customSchema)]} />
        );
    }

    return <JsonLd data={schema} />;
}
