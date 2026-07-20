import ProjectDetailsLink from "@/components/Projects/ProjectDetailsLink";
import Icon from "@/components/ui/Icon";
import Image from "@/components/ui/Image";

import type { FeaturedProject } from "@/lib/projects";
import { getYouTubeWatchUrl } from "@/lib/youtube";

export default function FeaturedProjectCard({
    project,
}: {
    project: FeaturedProject;
}) {
    const videoUrl = getYouTubeWatchUrl(project.videoUrl);

    return (
        <article className="glass-card group relative overflow-hidden rounded-xl transition-transform duration-300 motion-safe:hover:scale-[1.02]">
            <div className="relative aspect-[16/10] overflow-hidden">
                <Image
                    alt={project.imageAlt}
                    className="object-cover transition-transform duration-700 group-hover:scale-105"
                    fill
                    sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
                    src={project.image}
                    unoptimized={project.image.endsWith(".svg")}
                />
                {videoUrl ? (
                    <a
                        aria-label={project.title || videoUrl}
                        className="absolute inset-0 flex items-center justify-center bg-black/10 transition-colors hover:bg-black/25 focus-visible:bg-black/25"
                        href={videoUrl}
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        <span className="flex size-16 items-center justify-center rounded-full border border-white/30 bg-black/60 text-white backdrop-blur">
                            <Icon className="text-4xl" name="play_circle" />
                        </span>
                    </a>
                ) : null}
            </div>
            <div className="p-unit-lg">
                {project.category ? (
                    <p className="mb-2 font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">
                        {project.category}
                    </p>
                ) : null}
                {project.title ? (
                    <h3 className="mb-4 font-headline-md text-headline-md text-white">
                        {project.title}
                    </h3>
                ) : null}
                {project.specs.length ? (
                    <dl className="space-y-3 border-t border-outline-variant/20 pt-4">
                    {project.specs.filter((spec) => spec.label || spec.value).map((spec) => (
                        <div
                            className="flex items-center justify-between gap-4"
                            key={`${spec.label}-${spec.value}`}
                        >
                            {spec.label ? (
                                <dt className="text-label-md text-on-surface-variant">
                                    {spec.label}
                                </dt>
                            ) : null}
                            {spec.value ? (
                                <dd className="text-right font-bold text-white">
                                    {spec.value}
                                </dd>
                            ) : null}
                        </div>
                    ))}
                    </dl>
                ) : null}
                <ProjectDetailsLink
                    className="mt-5 inline-flex items-center gap-2 font-bold text-secondary transition-colors hover:text-primary"
                    slug={project.slug}
                />
            </div>
        </article>
    );
}
