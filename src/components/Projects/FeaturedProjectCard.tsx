import Image from "@/components/ui/Image";

import type { FeaturedProject } from "@/lib/projects";

export default function FeaturedProjectCard({
    project,
}: {
    project: FeaturedProject;
}) {
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
            </div>
            <div className="p-unit-lg">
                <p className="mb-2 font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">
                    {project.category}
                </p>
                <h3 className="mb-4 font-headline-md text-headline-md text-white">
                    {project.title}
                </h3>
                <dl className="space-y-3 border-t border-outline-variant/20 pt-4">
                    {project.specs.map((spec) => (
                        <div
                            className="flex items-center justify-between gap-4"
                            key={spec.label}
                        >
                            <dt className="text-label-md text-on-surface-variant">
                                {spec.label}
                            </dt>
                            <dd className="text-right font-bold text-white">
                                {spec.value}
                            </dd>
                        </div>
                    ))}
                </dl>
            </div>
        </article>
    );
}
