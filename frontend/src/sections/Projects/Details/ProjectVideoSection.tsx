import TranslatedText from "@/components/i18n/TranslatedText";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getYouTubeEmbedUrl, getYouTubeWatchUrl } from "@/lib/youtube";

export default function ProjectVideoSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const embedUrl = getYouTubeEmbedUrl(project.videoUrl);
    const watchUrl = getYouTubeWatchUrl(project.videoUrl);

    if (!embedUrl) return null;

    const title = project.title || project.name;
    const description = project.seoDescription || project.description;

    return (
        <section
            aria-labelledby="project-video-title"
            className="mx-auto max-w-container-max px-4 pb-8 pt-6 sm:px-6 sm:pb-12 sm:pt-8 lg:px-margin-desktop"
        >
            <div className="mx-auto max-w-5xl">
                <div className="mb-5 max-w-3xl space-y-2">
                    <p className="text-sm font-semibold uppercase tracking-[0.14em] text-secondary">
                        <TranslatedText
                            fallback={{
                                ka: "პროექტის ვიდეო",
                                en: "Project video",
                                ru: "Видео проекта",
                            }}
                            translationKey="projects.detail.video.eyebrow"
                        />
                    </p>
                    <h2
                        className="text-2xl font-semibold leading-tight text-on-surface sm:text-3xl"
                        id="project-video-title"
                    >
                        {title}
                    </h2>
                    {description ? (
                        <p className="text-base leading-7 text-on-surface-variant">
                            {description}
                        </p>
                    ) : null}
                </div>

                <div className="relative aspect-video w-full overflow-hidden rounded-2xl border border-outline-variant/10 bg-black shadow-xl">
                    <iframe
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowFullScreen
                        className="absolute inset-0 h-full w-full"
                        loading="eager"
                        referrerPolicy="strict-origin-when-cross-origin"
                        src={embedUrl}
                        title={title}
                    />
                </div>

                {watchUrl ? (
                    <p className="mt-3 text-sm text-on-surface-variant">
                        <a
                            className="inline-flex min-h-9 items-center underline decoration-outline-variant underline-offset-4 transition hover:text-secondary"
                            href={watchUrl}
                            rel="noopener noreferrer"
                            target="_blank"
                        >
                            <TranslatedText
                                fallback={{
                                    ka: "ვიდეოს YouTube-ზე გახსნა",
                                    en: "Open video on YouTube",
                                    ru: "Открыть видео на YouTube",
                                }}
                                translationKey="projects.detail.video.youtubeLink"
                            />
                        </a>
                    </p>
                ) : null}
            </div>
        </section>
    );
}
