import type { ServiceDetail } from "../model/types";

export default function ServiceOverview({
    service,
}: {
    service: ServiceDetail;
}) {
    const hasOverviewContent =
        Boolean(service.overview.title) ||
        service.overview.paragraphs.length > 0 ||
        service.overview.stats.length > 0;

    if (!hasOverviewContent) return null;

    return (
        <section
            aria-labelledby={
                service.overview.title ? "service-overview-title" : undefined
            }
            className="scroll-reveal mx-auto w-full max-w-container-max px-5 py-14 md:px-8 md:py-16 xl:px-14"
        >
            <div className="glass-card grid gap-8 rounded-3xl p-6 sm:p-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.65fr)] lg:gap-12 lg:p-10">
                <div className="space-y-unit-lg">
                    {service.overview.title ? (
                        <h2
                            className="max-w-4xl font-headline-xl text-[30px] leading-tight text-white sm:text-[38px] md:text-[46px] lg:text-[48px]"
                            id="service-overview-title"
                        >
                            {service.overview.title}
                        </h2>
                    ) : null}
                    {service.overview.paragraphs.length ? (
                        <div className="max-w-4xl space-y-unit-md font-body-md leading-relaxed text-on-surface-variant">
                            {service.overview.paragraphs.map((paragraph) => (
                                <p key={paragraph}>{paragraph}</p>
                            ))}
                        </div>
                    ) : null}
                </div>

                {service.overview.stats.length ? (
                    <dl className="grid grid-cols-2 gap-4 self-start sm:gap-unit-lg lg:grid-cols-2">
                        {service.overview.stats.map((stat) => (
                            <div
                                className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-4"
                                key={`${stat.value}-${stat.label}`}
                            >
                                <dt className="mt-1 text-label-md text-on-surface-variant">
                                    {stat.label}
                                </dt>
                                <dd className="text-2xl font-bold text-secondary">
                                    {stat.value}
                                </dd>
                            </div>
                        ))}
                    </dl>
                ) : null}
            </div>
        </section>
    );
}
