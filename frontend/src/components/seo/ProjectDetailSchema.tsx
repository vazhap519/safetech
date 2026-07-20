import type { ProjectDetail } from "@/lib/projectDetails";
import JsonLd from "@/components/seo/JsonLd";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { buildBreadcrumbSchema } from "@/lib/structured-data";
import { createTranslator } from "@/lib/translations";
import { getYouTubeEmbedUrl, getYouTubeWatchUrl } from "@/lib/youtube";

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
    const schema = {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "CreativeWork",
                "@id": `${url}#project`,
                name: project.title || project.name,
                description: project.seoDescription,
                image: absoluteSiteUrl(project.image || branding.defaultImage),
                url,
                ...(videoWatchUrl
                    ? {
                          video: {
                              "@type": "VideoObject",
                              name: project.title || project.name,
                              description: project.seoDescription,
                              thumbnailUrl: absoluteSiteUrl(
                                  project.image || branding.defaultImage,
                              ),
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
                    logo: absoluteSiteUrl(
                        branding.logo ||
                            branding.footerLogo ||
                            branding.defaultImage,
                    ),
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
        return <JsonLd data={project.seo.schema} />;
    }

    return <JsonLd data={schema} />;
}
