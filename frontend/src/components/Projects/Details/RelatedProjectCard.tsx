import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import ProjectDetailsLink from "@/components/Projects/ProjectDetailsLink";
import type { ProjectDetail } from "@/lib/projectDetails";

type RelatedProject = ProjectDetail["related"][number];

export default function RelatedProjectCard({
    project,
}: {
    project: RelatedProject;
}) {
    if (!project.title && !project.category) return null;

    if (!project.slug) return null;

    return (
            <article className="glass-card group flex h-full flex-col rounded-lg p-4">
                {project.image ? (
                    <LocalizedLink href={`/projects/${project.slug}`}>
                        <div className="relative mb-5 aspect-video overflow-hidden rounded-lg">
                            <Image
                                alt={project.imageAlt || project.title}
                                className="object-cover transition-transform duration-500 group-hover:scale-105"
                                fill
                                sizes="(max-width: 768px) 100vw, 33vw"
                                src={project.image}
                                unoptimized={project.image.endsWith(".svg")}
                            />
                        </div>
                    </LocalizedLink>
                ) : null}
                {project.title ? (
                    <LocalizedLink href={`/projects/${project.slug}`}>
                        <h3 className="mb-2 font-headline-md text-headline-md transition-colors group-hover:text-primary">
                            {project.title}
                        </h3>
                    </LocalizedLink>
                ) : null}
                {project.category ? (
                    <p className="mb-4 font-label-md text-label-md text-on-surface-variant">
                        {project.category}
                    </p>
                ) : null}
                <ProjectDetailsLink className="mt-auto" slug={project.slug} />
            </article>
    );
}
