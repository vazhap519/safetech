import ProjectStat from "@/components/Projects/Details/ProjectStat";
import Image from "@/components/ui/Image";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectOverviewSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const scopeTitle = translateText(
        translations,
        "project.detail.overview.scopeTitle",
        locale,
        null,
    );
    const specsTitle = translateText(
        translations,
        "project.detail.overview.specsTitle",
        locale,
        null,
    );
    const scope = project.scope.filter((item) => item.label || item.value);
    const specs = project.specs.filter((item) => item.label || item.value);
    const isSvgImage = project.image.endsWith(".svg");

    if (!project.image && !scope.length && !specs.length) return null;

    return (
        <section
            aria-labelledby={specsTitle ? "technical-specs-title" : undefined}
            className="bg-surface-container-lowest/50 px-margin-desktop py-unit-xl"
        >
            <div className="mx-auto grid max-w-container-max gap-unit-xl lg:grid-cols-2">
                {project.image || scope.length ? (
                    <div className="grid h-fit grid-cols-1 gap-unit-sm min-[420px]:grid-cols-2">
                        {project.image ? (
                            <div className="relative aspect-square overflow-hidden rounded-xl border border-outline-variant/10">
                                <Image
                                    alt={project.imageAlt || project.title || project.name}
                                    className="object-cover"
                                    fill
                                    sizes="(max-width: 1024px) 50vw, 25vw"
                                    src={project.image}
                                    unoptimized={isSvgImage}
                                />
                            </div>
                        ) : null}
                        {scope.length ? (
                            <div className="glass-card flex flex-col justify-center rounded-xl p-unit-md">
                                {scopeTitle ? (
                                    <h2 className="mb-4 font-mono-sm text-mono-sm uppercase text-outline">
                                        {scopeTitle}
                                    </h2>
                                ) : null}
                                <dl className="space-y-4">
                                    {scope.map((item) => (
                                        <div key={`${item.label}-${item.value}`}>
                                            {item.label ? (
                                                <dt className="order-2 font-label-md text-label-md text-on-surface-variant">
                                                    {item.label}
                                                </dt>
                                            ) : null}
                                            {item.value ? (
                                                <dd className="font-headline-md text-headline-md text-primary-fixed-dim">
                                                    {item.value}
                                                </dd>
                                            ) : null}
                                        </div>
                                    ))}
                                </dl>
                            </div>
                        ) : null}
                    </div>
                ) : null}
                {specs.length ? (
                    <div className="flex flex-col justify-center">
                        {specsTitle ? (
                            <h2
                                className="mb-8 font-headline-xl text-headline-xl"
                                id="technical-specs-title"
                            >
                                {specsTitle}
                            </h2>
                        ) : null}
                        <div className="grid grid-cols-2 gap-unit-md sm:grid-cols-3">
                            {specs.map((stat) => (
                                <ProjectStat
                                    key={`${stat.label}-${stat.value}`}
                                    {...stat}
                                />
                            ))}
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
