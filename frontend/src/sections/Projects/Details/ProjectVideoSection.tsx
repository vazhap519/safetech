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
            className="mx-auto max-w-container-max px-4 pb-10 pt-6 sm:px-6 sm:pb-14 sm:pt-8 lg:px-margin-desktop"
        >
            <div className="relative mx-auto max-w-6xl overflow-hidden rounded-[28px] border border-outline-variant/10 bg-surface-container-low p-3 shadow-[0_24px_80px_rgba(0,0,0,0.22)] sm:p-5 lg:p-6">
                <div className="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-secondary/10 blur-3xl" />
                <div className="pointer-events-none absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-primary/10 blur-3xl" />

                <div className="relative grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-center">
                    <div className="overflow-hidden rounded-[22px] border border-white/10 bg-black shadow-2xl">
                        <div className="relative aspect-video w-full">
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
                    </div>

                    <div className="flex h-full flex-col justify-center px-2 pb-2 sm:px-1 lg:px-0 lg:py-3">
                        <div className="mb-4 flex flex-wrap items-center gap-2">
                            <span className="inline-flex items-center gap-2 rounded-full border border-secondary/20 bg-secondary/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-secondary">
                                <span aria-hidden="true" className="size-2 rounded-full bg-secondary shadow-[0_0_16px_currentColor]" />
                                <TranslatedText
                                    fallback={{
                                        ka: "პროექტის ვიდეო",
                                        en: "Project video",
                                        ru: "Видео проекта",
                                    }}
                                    translationKey="projects.detail.video.eyebrow"
                                />
                            </span>
                            <span className="rounded-full border border-outline-variant/10 bg-surface-container px-3 py-1.5 text-xs text-on-surface-variant">
                                SafeTech
                            </span>
                        </div>

                        <h2
                            className="text-2xl font-semibold leading-tight text-on-surface sm:text-3xl"
                            id="project-video-title"
                        >
                            {title}
                        </h2>

                        {description ? (
                            <p className="mt-3 line-clamp-5 text-sm leading-7 text-on-surface-variant sm:text-[15px]">
                                {description}
                            </p>
                        ) : null}

                        {watchUrl ? (
                            <a
                                className="mt-6 inline-flex min-h-11 w-fit items-center gap-2 rounded-full border border-outline-variant/15 bg-surface-container px-4 py-2.5 text-sm font-semibold text-on-surface transition hover:-translate-y-0.5 hover:border-secondary/40 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                href={watchUrl}
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                <span aria-hidden="true">▶</span>
                                <TranslatedText
                                    fallback={{
                                        ka: "YouTube-ზე ნახვა",
                                        en: "Watch on YouTube",
                                        ru: "Смотреть на YouTube",
                                    }}
                                    translationKey="projects.detail.video.youtubeLink"
                                />
                                <span aria-hidden="true">↗</span>
                            </a>
                        ) : null}
                    </div>
                </div>
            </div>
        </section>
    );
}
