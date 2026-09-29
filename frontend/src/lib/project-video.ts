import type { Locale } from "@/lib/locales";
import { getYouTubeEmbedUrl, getYouTubeThumbnailUrl } from "@/lib/youtube";

type VideoProject = {
    slug: string;
    title?: string | null;
    name?: string | null;
    description?: string | null;
    seoDescription?: string | null;
    videoUrl?: string | null;
    video_url?: string | null;
    publishedAt?: string | null;
};

export function projectVideoPath(slug: string) {
    return `/videos/${encodeURIComponent(slug)}`;
}

export function getProjectVideo(project: VideoProject, locale: Locale) {
    const videoUrl = project.videoUrl || project.video_url;
    const embedUrl = getYouTubeEmbedUrl(videoUrl);
    const thumbnailUrl = getYouTubeThumbnailUrl(videoUrl);
    if (!embedUrl || !thumbnailUrl) return null;

    const label = { ka: "პროექტის ვიდეო", en: "Project video", ru: "Видео проекта" }[locale];
    const name = project.title || project.name || project.slug;
    const summary = (project.seoDescription || project.description || name)
        .replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    const publishedAt = project.publishedAt;

    return {
        path: projectVideoPath(project.slug),
        title: Array.from(`${label}: ${name}`).slice(0, 100).join(""),
        description: Array.from(`${label}: ${summary}`).slice(0, 2048).join(""),
        embedUrl,
        thumbnailUrl,
        // Use the recorded publication on this site, never an edit or crawl date.
        ...(publishedAt && !Number.isNaN(Date.parse(publishedAt))
            ? { uploadDate: new Date(publishedAt).toISOString() }
            : {}),
    };
}
