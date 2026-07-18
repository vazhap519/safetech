"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { Project } from "@/lib/projects";
import { getYouTubeWatchUrl } from "@/lib/youtube";

export default function ProjectCard({ project }: { project: Project }) {
    const color = project.accent === "secondary" ? "secondary" : "primary";
    const { t } = useLocalization();
    const detailsLabel = t("common.details", null);
    const completedLabel = t("projects.completed", null);
    const videoLabel = t("projects.video.open", null);
    const videoUrl = getYouTubeWatchUrl(project.videoUrl);

    return (
        <article
            className={`glass-card group rounded-xl border-l-4 p-6 ${
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
                    {project.title}
                </h3>
            ) : null}
            {project.description ? (
                <p className="mb-4 text-body-md leading-relaxed text-on-surface-variant">
                    {project.description}
                </p>
            ) : null}
            {project.slug && detailsLabel ? (
                <LocalizedLink
                    className="inline-flex items-center gap-2 font-bold text-secondary"
                    href={`/projects/${project.slug}`}
                >
                    {detailsLabel} <span aria-hidden="true">-&gt;</span>
                </LocalizedLink>
            ) : completedLabel ? (
                <span className="inline-flex items-center gap-2 font-bold text-secondary/70">
                    {completedLabel}
                </span>
            ) : null}
        </article>
    );
}
