import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

import type { ServiceDetail } from "../model/types";

export default async function ServiceOverview({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const imageAltSuffix = translateText(
        translations,
        "service.detail.overview.imageAltSuffix",
        locale,
        null,
    );

    return (
        <section
            aria-labelledby={
                service.overview.title ? "service-overview-title" : undefined
            }
            className="scroll-reveal mx-auto grid w-full max-w-container-max items-center gap-10 px-5 py-14 md:px-8 md:py-16 lg:grid-cols-[minmax(360px,0.9fr)_minmax(0,1.1fr)] lg:gap-14 xl:px-14"
        >
            <div className="group glass-card relative mx-auto aspect-[14/9] w-full max-w-[560px] overflow-hidden rounded-2xl bg-surface-container-lowest/70 p-1 lg:mx-0">
                <Image
                    alt={[service.name, imageAltSuffix].filter(Boolean).join(" ")}
                    className="h-full w-full rounded-xl object-contain transition-transform duration-700 group-hover:scale-[1.02]"
                    height={900}
                    sizes="(max-width: 1023px) 100vw, (max-width: 1440px) 42vw, 560px"
                    src={service.heroImage}
                    unoptimized={service.heroImage.endsWith(".svg")}
                    width={1400}
                />
                <div
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 bg-gradient-to-t from-background/30 via-transparent to-transparent"
                />
            </div>
            <div className="space-y-unit-lg">
                {service.overview.title ? (
                    <h2
                        className="font-headline-xl text-[30px] leading-tight text-white sm:text-[38px] md:text-[46px] lg:text-[48px]"
                        id="service-overview-title"
                    >
                        {service.overview.title}
                    </h2>
                ) : null}
                {service.overview.paragraphs.length ? (
                    <div className="max-w-3xl space-y-unit-md font-body-md leading-relaxed text-on-surface-variant">
                        {service.overview.paragraphs.map((paragraph) => (
                            <p key={paragraph}>{paragraph}</p>
                        ))}
                    </div>
                ) : null}
                {service.overview.stats.length ? (
                    <dl className="grid grid-cols-2 gap-unit-lg sm:grid-cols-4">
                        {service.overview.stats.map((stat) => (
                            <div className="flex flex-col" key={stat.label}>
                                <dt className="order-2 text-label-md text-on-surface-variant">
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
