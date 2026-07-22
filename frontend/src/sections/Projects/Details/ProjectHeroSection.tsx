import ProjectBreadcrumb from "@/components/Projects/Details/ProjectBreadcrumb";
import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import Image from "@/components/ui/Image";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectHeroSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const badge = translateText(
        translations,
        "project.detail.hero.badge",
        locale,
        null,
    );
    const primaryCta = translateText(
        translations,
        "project.detail.hero.primaryCta",
        locale,
        null,
    );
    const secondaryCta = translateText(
        translations,
        "project.detail.hero.secondaryCta",
        locale,
        null,
    );
    const meta = project.meta.filter((item) => item.label || item.value);
    const hasHeroText = badge || project.title || project.description;

    if (!hasHeroText && !project.image && !meta.length) return null;

    return (
        <>
            <section className="mx-auto mt-8 max-w-container-max px-margin-desktop py-unit-sm">
                <ProjectBreadcrumb current={project.name} />
            </section>
            <section className="relative mx-auto max-w-container-max overflow-hidden px-margin-desktop py-unit-lg">
                <div
                    aria-hidden="true"
                    className="ambient-glow -right-24 -top-48 h-[500px] w-[500px] bg-primary-container"
                />
                <div className="grid items-center gap-unit-xl lg:grid-cols-2">
                    {hasHeroText || meta.length || primaryCta || secondaryCta ? (
                        <div>
                            {badge ? (
                                <p className="mb-6 inline-flex items-center gap-2 rounded-full border border-primary-container/30 bg-primary-container/10 px-3 py-1 font-mono-sm text-mono-sm text-primary-fixed-dim">
                                    <span
                                        aria-hidden="true"
                                        className="h-2 w-2 animate-pulse rounded-full bg-primary-container"
                                    />
                                    {badge}
                                </p>
                            ) : null}
                            {project.title ? (
                                <h1 className="mb-7 font-display-lg text-[34px] font-semibold leading-[1.16] text-white sm:text-[40px] md:text-[48px] lg:text-[54px]">
                                    {project.title}
                                </h1>
                            ) : null}
                            {project.description ? (
                                <p className="mb-unit-lg max-w-2xl text-body-lg leading-relaxed text-on-surface-variant">
                                    {project.description}
                                </p>
                            ) : null}
                            {meta.length ? (
                                <dl className="mb-10 grid grid-cols-1 gap-unit-md border-l border-outline-variant/30 pl-6 min-[420px]:grid-cols-3">
                                    {meta.map((item) => (
                                        <div key={`${item.label}-${item.value}`}>
                                            {item.label ? (
                                                <dt className="mb-1 font-mono-sm text-mono-sm uppercase tracking-widest text-outline">
                                                    {item.label}
                                                </dt>
                                            ) : null}
                                            {item.value ? (
                                                <dd className="font-headline-md text-headline-md">
                                                    {item.value}
                                                </dd>
                                            ) : null}
                                        </div>
                                    ))}
                                </dl>
                            ) : null}
                            {primaryCta || secondaryCta ? (
                                <div className="flex flex-col gap-unit-md sm:flex-row">
                                    {primaryCta ? (
                                        <ConsultationTrigger className="rounded-xl bg-primary-container px-6 py-4 text-center font-label-md text-on-primary-container transition-shadow hover:shadow-2xl hover:shadow-primary-container/40">
                                            {primaryCta}
                                        </ConsultationTrigger>
                                    ) : null}
                                    {secondaryCta ? (
                                        <ConsultationTrigger className="rounded-xl border border-outline-variant bg-surface-container-low px-6 py-4 text-center font-label-md text-on-surface transition-colors hover:bg-surface-variant/30">
                                            {secondaryCta}
                                        </ConsultationTrigger>
                                    ) : null}
                                </div>
                            ) : null}
                        </div>
                    ) : null}
                    {project.image ? (
                        <div className="group relative">
                            <div
                                aria-hidden="true"
                                className="absolute inset-0 z-10 bg-gradient-to-t from-background via-transparent to-transparent opacity-60"
                            />
                            <Image
                                alt={project.imageAlt || project.title || project.name}
                                className="aspect-[4/3] h-auto w-full rounded-2xl border border-outline-variant/20 object-cover shadow-2xl transition-transform duration-700 group-hover:scale-[1.01]"
                                height={900}
                                priority
                                sizes="(max-width: 1024px) 100vw, 50vw"
                                src={project.image}
                                unoptimized={project.image.endsWith(".svg")}
                                width={1200}
                            />
                        </div>
                    ) : null}
                </div>
            </section>
        </>
    );
}
