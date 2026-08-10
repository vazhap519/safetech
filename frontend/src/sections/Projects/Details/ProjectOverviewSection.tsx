import ProjectStat from "@/components/Projects/Details/ProjectStat";
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

    if (!scope.length && !specs.length) return null;

    return (
        <section
            aria-labelledby={specsTitle ? "technical-specs-title" : undefined}
            className="bg-surface-container-lowest/50 py-10 sm:py-unit-xl"
            style={{ contentVisibility: "auto", containIntrinsicSize: "640px" }}
        >
            <div className="mx-auto grid max-w-container-max gap-6 px-4 sm:px-6 lg:grid-cols-5 lg:gap-unit-xl lg:px-margin-desktop">
                {scope.length ? (
                    <div className="glass-card flex min-w-0 flex-col justify-center rounded-2xl p-5 sm:p-unit-md lg:col-span-2">
                        {scopeTitle ? (
                            <h2 className="mb-4 font-mono-sm text-mono-sm uppercase text-outline">
                                {scopeTitle}
                            </h2>
                        ) : null}
                        <dl className="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            {scope.map((item) => (
                                <div className="min-w-0" key={`${item.label}-${item.value}`}>
                                    {item.label ? (
                                        <dt className="break-words font-label-md text-label-md text-on-surface-variant">
                                            {item.label}
                                        </dt>
                                    ) : null}
                                    {item.value ? (
                                        <dd className="mt-1 break-words font-headline-md text-headline-md text-primary-fixed-dim">
                                            {item.value}
                                        </dd>
                                    ) : null}
                                </div>
                            ))}
                        </dl>
                    </div>
                ) : null}
                {specs.length ? (
                    <div className={`flex min-w-0 flex-col justify-center ${scope.length ? "lg:col-span-3" : "lg:col-span-5"}`}>
                        {specsTitle ? (
                            <h2
                                className="mb-6 text-[30px] font-semibold leading-tight sm:mb-8 sm:font-headline-xl sm:text-headline-xl"
                                id="technical-specs-title"
                            >
                                {specsTitle}
                            </h2>
                        ) : null}
                        <div className="grid grid-cols-1 gap-4 min-[420px]:grid-cols-2 sm:grid-cols-3 sm:gap-unit-md">
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
