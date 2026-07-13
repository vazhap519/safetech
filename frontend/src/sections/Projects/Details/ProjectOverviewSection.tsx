import Image from "@/components/ui/Image";

import ProjectStat from "@/components/Projects/Details/ProjectStat";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function ProjectOverviewSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const isSvgImage = project.image.endsWith(".svg");

    return (
        <section
            aria-labelledby="technical-specs-title"
            className="bg-surface-container-lowest/50 px-margin-desktop py-unit-xl"
        >
            <div className="mx-auto grid max-w-container-max gap-unit-xl lg:grid-cols-2">
                <div className="grid h-fit grid-cols-1 gap-unit-sm min-[420px]:grid-cols-2">
                    <div className="relative aspect-square overflow-hidden rounded-xl border border-outline-variant/10">
                        <Image
                            alt="პროექტის ტექნიკური ინფრასტრუქტურა"
                            className="object-cover"
                            fill
                            sizes="(max-width: 1024px) 50vw, 25vw"
                            src={project.image}
                            unoptimized={isSvgImage}
                        />
                    </div>
                    <div className="glass-card flex flex-col justify-center rounded-xl p-unit-md">
                        <h2 className="mb-4 font-mono-sm text-mono-sm uppercase text-outline">
                            პროექტის მასშტაბი
                        </h2>
                        <dl className="space-y-4">
                            {project.scope.map((item) => (
                                <div key={item.label}>
                                    <dt className="order-2 font-label-md text-label-md text-on-surface-variant">
                                        {item.label}
                                    </dt>
                                    <dd className="font-headline-md text-headline-md text-primary-fixed-dim">
                                        {item.value}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </div>
                </div>
                <div className="flex flex-col justify-center">
                    <h2
                        className="mb-8 font-headline-xl text-headline-xl"
                        id="technical-specs-title"
                    >
                        ტექნიკური მახასიათებლები
                    </h2>
                    <div className="grid grid-cols-2 gap-unit-md sm:grid-cols-3">
                        {project.specs.map((stat) => (
                            <ProjectStat key={stat.label} {...stat} />
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
