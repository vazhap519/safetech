import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { ProjectDetail } from "@/lib/projectDetails";

type RelatedProject = ProjectDetail["related"][number];

export default function RelatedProjectCard({
    project,
}: {
    project: RelatedProject;
}) {
    if (!project.title && !project.category) return null;

    return (
        <LocalizedLink className="group block" href={`/projects/${project.slug}`}>
            <article>
                {project.image ? (
                    <div className="relative mb-6 aspect-video overflow-hidden rounded-xl">
                        <Image
                            alt={project.imageAlt || project.title}
                            className="object-cover transition-transform duration-500 group-hover:scale-105"
                            fill
                            sizes="(max-width: 768px) 100vw, 33vw"
                            src={project.image}
                            unoptimized={project.image.endsWith(".svg")}
                        />
                    </div>
                ) : null}
                {project.title ? (
                    <h3 className="mb-2 font-headline-md text-headline-md transition-colors group-hover:text-primary">
                        {project.title}
                    </h3>
                ) : null}
                {project.category ? (
                    <p className="font-label-md text-label-md text-on-surface-variant">
                        {project.category}
                    </p>
                ) : null}
            </article>
        </LocalizedLink>
    );
}
