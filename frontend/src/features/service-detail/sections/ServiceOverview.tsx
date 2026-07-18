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
            className="scroll-reveal grid items-center gap-unit-xl px-margin-desktop py-unit-xl md:grid-cols-2"
        >
            <div className="group glass-card relative aspect-video overflow-hidden rounded-2xl">
                <Image
                    alt={[service.name, imageAltSuffix].filter(Boolean).join(" ")}
                    className="object-cover transition-transform duration-700 group-hover:scale-105"
                    fill
                    sizes="(max-width: 768px) 100vw, 50vw"
                    src={service.heroImage}
                    unoptimized={service.heroImage.endsWith(".svg")}
                />
                <div
                    aria-hidden="true"
                    className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent opacity-60"
                />
            </div>
            <div className="space-y-unit-lg">
                {service.overview.title ? (
                    <h2
                        className="font-headline-xl text-[30px] leading-tight text-white sm:text-[38px] md:text-[52px]"
                        id="service-overview-title"
                    >
                        {service.overview.title}
                    </h2>
                ) : null}
                {service.overview.paragraphs.length ? (
                    <div className="space-y-unit-md font-body-md leading-relaxed text-on-surface-variant">
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
