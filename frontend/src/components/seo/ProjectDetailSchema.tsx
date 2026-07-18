import type { ProjectDetail } from "@/lib/projectDetails";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
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
            {
                "@type": "BreadcrumbList",
                itemListElement: [
                    {
                        "@type": "ListItem",
                        position: 1,
                        name: t("nav.home", {
                            ka: "მთავარი",
                            en: "Home",
                            ru: "Главная",
                        }),
                        item: absoluteLocalizedUrl("/", locale),
                    },
                    {
                        "@type": "ListItem",
                        position: 2,
                        name: t("nav.projects", {
                            ka: "პროექტები",
                            en: "Projects",
                            ru: "Проекты",
                        }),
                        item: absoluteLocalizedUrl("/projects", locale),
                    },
                    {
                        "@type": "ListItem",
                        position: 3,
                        name: project.title || project.name,
                        item: url,
                    },
                ],
            },
        ],
    };

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
