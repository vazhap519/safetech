import ProjectDetailsLink from "@/components/Projects/ProjectDetailsLink";
import Icon from "@/components/ui/Icon";
import type { Project } from "@/lib/projects";
import { localizeHref } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import { getYouTubeWatchUrl } from "@/lib/youtube";
import Link from "next/link";

export default async function ProjectCard({ project }: { project: Project }) {
    const color = project.accent === "secondary" ? "secondary" : "primary";
    const { locale, translations } = await getSiteSettings();
    const completedLabel = translateText(
        translations,
        "projects.completed",
        locale,
        null,
    );
    const videoLabel = translateText(
        translations,
        "projects.video.open",
        locale,
        null,
    );
    const videoUrl = getYouTubeWatchUrl(project.videoUrl);

    return (
        <article
            className={`glass-card group rounded-lg border-l-4 p-6 ${
                color === "secondary" ? "border-secondary" : "border-primary"
            }`}
        >
            <div className="mb-4 flex items-start justify-between gap-4">
                <Icon
                    className={`text-4xl ${
                        color === "secondary" ? "text-secondary" : "text-primary"
                    }`}
                    name={project.icon}
                />
                {project.technology || videoUrl ? (
                    <div className="flex items-center gap-2">
                        {project.technology ? (
                            <span className="rounded bg-surface-container-high px-2 py-1 text-[10px] font-bold uppercase text-on-surface-variant">
                                {project.technology}
                            </span>
                        ) : null}
                        {videoUrl ? (
                            <a
                                aria-label={videoLabel || project.title || videoUrl}
                                className="inline-flex size-9 items-center justify-center rounded-full border border-primary/30 bg-primary/10 text-primary transition-colors hover:bg-primary hover:text-on-primary"
                                href={videoUrl}
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                <Icon name="play_circle" />
                                {videoLabel ? (
                                    <span className="sr-only">{videoLabel}</span>
                                ) : null}
                            </a>
                        ) : null}
                    </div>
                ) : null}
            </div>
            {project.title ? (
                <h3 className="mb-2 font-headline-md text-headline-md text-white">
                    {project.slug ? (
                        <Link
                            className="transition-colors hover:text-secondary"
                            href={localizeHref(`/projects/${project.slug}`, locale)}
                            prefetch={false}
                        >
                            {project.title}
                        </Link>
                    ) : (
                        project.title
                    )}
                </h3>
            ) : null}
            {project.description ? (
                <p className="mb-4 text-body-md leading-relaxed text-on-surface-variant">
                    {project.description}
                </p>
            ) : null}
            {project.slug ? (
                <ProjectDetailsLink slug={project.slug} />
            ) : completedLabel ? (
                <span className="inline-flex items-center gap-2 font-bold text-secondary/70">
                    {completedLabel}
                </span>
            ) : null}
        </article>
    );
}
