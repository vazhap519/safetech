import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";

import JsonLd from "@/components/seo/JsonLd";
import { confirmBackendResourceNotFound } from "@/lib/backend-resource-status";
import { getLanguageTag, localizePath, normalizeLocale } from "@/lib/locales";
import { getLocalizedProject } from "@/lib/project-api";
import { getProjectVideo } from "@/lib/project-video";
import { absoluteLocalizedUrl, createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { buildBreadcrumbSchema } from "@/lib/structured-data";

type VideoPageProps = { params: Promise<{ slug: string; locale?: string }> };

async function getVideoPage(params: VideoPageProps["params"]) {
    const { slug, locale: routeLocale } = await params;
    const [settings, project] = await Promise.all([
        getSiteSettings(), getLocalizedProject(slug, routeLocale),
    ]);
    const locale = normalizeLocale(routeLocale || settings.locale);
    if (!project) {
        await confirmBackendResourceNotFound(`/projects/${encodeURIComponent(slug)}`, { locale });
        notFound();
    }
    const video = getProjectVideo(project, locale);
    if (!video) notFound();

    return { settings, project, video, locale };
}

export async function generateMetadata({ params }: VideoPageProps): Promise<Metadata> {
    const { settings, project, video, locale } = await getVideoPage(params);
    const metadata = createMetadata({
        title: video.title,
        description: video.description,
        path: video.path,
        locale,
        siteName: settings.branding.siteName,
        image: video.thumbnailUrl,
        noindex: Boolean(project.seo?.noindex),
    });

    return {
        ...metadata,
        openGraph: {
            ...metadata.openGraph,
            type: "video.other",
            images: [{ url: video.thumbnailUrl, width: 480, height: 360, alt: video.title }],
            videos: [{ url: video.embedUrl, width: 1280, height: 720, type: "text/html" }],
        },
    };
}

export default async function ProjectVideoPage({ params }: VideoPageProps) {
    const { settings, project, video, locale } = await getVideoPage(params);
    const url = absoluteLocalizedUrl(video.path, locale);
    const projectPath = `/projects/${encodeURIComponent(project.slug)}`;
    const labels = {
        ka: { projects: "პროექტები", details: "პროექტის სრული აღწერა" },
        en: { projects: "Projects", details: "Full project details" },
        ru: { projects: "Проекты", details: "Полное описание проекта" },
    }[locale];
    const videoId = `${url}#video`;

    return (
        <article className="mx-auto max-w-6xl px-4 pb-16 pt-24 sm:px-6 sm:pt-28">
            <JsonLd data={{
                "@context": "https://schema.org",
                "@graph": [
                    {
                        "@type": "WebPage", "@id": url, url,
                        name: video.title, description: video.description,
                        ...(video.uploadDate ? { mainEntity: { "@id": videoId } } : {}),
                        inLanguage: getLanguageTag(locale),
                    },
                    ...(video.uploadDate ? [{
                        "@type": "VideoObject", "@id": videoId,
                        name: video.title, description: video.description,
                        thumbnailUrl: video.thumbnailUrl,
                        uploadDate: video.uploadDate,
                        url, embedUrl: video.embedUrl,
                        mainEntityOfPage: { "@id": url },
                        publisher: { "@type": "Organization", name: settings.branding.siteName },
                        inLanguage: getLanguageTag(locale),
                    }] : []),
                    buildBreadcrumbSchema([
                        { name: labels.projects, url: absoluteLocalizedUrl("/projects", locale) },
                        { name: project.title || project.name, url: absoluteLocalizedUrl(projectPath, locale) },
                        { name: video.title, url },
                    ]),
                ],
            }} />
            <h1 className="mb-5 text-xl font-semibold leading-snug text-on-surface sm:text-3xl">{video.title}</h1>
            <div className="relative aspect-video w-full overflow-hidden rounded-2xl border border-outline-variant/20 bg-black">
                <iframe
                    className="absolute inset-0 h-full w-full"
                    src={video.embedUrl}
                    title={video.title}
                    width="1280"
                    height="720"
                    loading="eager"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowFullScreen
                    referrerPolicy="strict-origin-when-cross-origin"
                />
            </div>
            <p className="mt-6 max-w-3xl text-base leading-7 text-on-surface-variant">{video.description}</p>
            <Link
                href={localizePath(projectPath, locale)}
                className="mt-6 inline-flex min-h-11 items-center rounded-full border border-outline-variant/30 px-5 py-3 text-sm font-semibold text-on-surface hover:border-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
            >
                {labels.details}
            </Link>
        </article>
    );
}
