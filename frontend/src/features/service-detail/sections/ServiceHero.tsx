import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import Icon from "@/components/ui/Icon";
import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

import ServiceBreadcrumb from "../components/ServiceBreadcrumb";
import ServiceShareButtons from "../components/ServiceShareButtons";
import type { ServiceDetail } from "../model/types";

export default async function ServiceHero({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const eyebrow = translateText(
        translations,
        `service.${service.slug}.eyebrow`,
        locale,
        service.eyebrow,
    );
    const highlightsLabel = translateText(
        translations,
        "service.hero.highlights",
        locale,
        null,
    );
    const consultationLabel = translateText(
        translations,
        "service.hero.consultation",
        locale,
        null,
    );
    const pricingLabel = translateText(
        translations,
        "service.hero.pricing",
        locale,
        null,
    );
    const shareLabel = translateText(
        translations,
        "service.share.title",
        locale,
        null,
    );

    return (
        <section className="relative flex min-h-[clamp(34rem,72svh,46rem)] flex-col justify-center overflow-hidden px-5 py-12 md:px-8 md:py-16 xl:px-14">
            <div aria-hidden="true" className="ambient-glow -left-20 -top-20" />
            <div className="mx-auto w-full max-w-container-max">
                <ServiceBreadcrumb current={service.name} />
                <div className="grid items-center gap-10 lg:grid-cols-[minmax(0,1.08fr)_minmax(420px,0.92fr)] lg:gap-12 xl:gap-16">
                    <div className="space-y-unit-lg">
                        {eyebrow ? (
                            <p className="inline-flex items-center gap-unit-sm rounded-full border border-primary/20 bg-primary/10 px-unit-md py-unit-xs font-label-md text-label-md uppercase tracking-wider text-primary">
                                <span aria-hidden="true" className="pulsing-dot" />{" "}
                                {eyebrow}
                            </p>
                        ) : null}
                        {service.title ? (
                            <h1 className="max-w-4xl font-display-lg text-[34px] font-semibold leading-[1.16] text-white sm:text-[40px] md:text-[48px] lg:text-[50px] xl:text-[54px]">
                                {service.title}
                            </h1>
                        ) : null}
                        {service.description ? (
                            <p className="max-w-2xl font-body-lg text-body-lg leading-relaxed text-on-surface-variant">
                                {service.description}
                            </p>
                        ) : null}
                        {service.highlights.length ? (
                            <ul
                                aria-label={highlightsLabel || undefined}
                                className="grid grid-cols-1 gap-unit-md py-unit-md sm:grid-cols-2"
                            >
                                {service.highlights.map((highlight) => (
                                    <li
                                        className="flex items-center gap-unit-sm font-label-md text-on-surface"
                                        key={highlight}
                                    >
                                        <Icon
                                            className="text-primary"
                                            name="check_circle"
                                        />
                                        {highlight}
                                    </li>
                                ))}
                            </ul>
                        ) : null}
                        {consultationLabel || pricingLabel ? (
                            <div className="flex flex-wrap gap-unit-md">
                                {consultationLabel ? (
                                    <ConsultationTrigger className="glow-button w-full rounded-xl bg-primary-container px-unit-lg py-unit-md text-center font-headline-md text-on-primary-container sm:w-auto sm:px-unit-xl">
                                        {consultationLabel}
                                    </ConsultationTrigger>
                                ) : null}
                                {pricingLabel ? (
                                    <LocalizedLink
                                        className="w-full rounded-xl border border-outline-variant bg-white/5 px-unit-lg py-unit-md text-center font-headline-md text-white transition-colors hover:bg-white/10 sm:w-auto sm:px-unit-xl"
                                        href="/contact#contact-form"
                                    >
                                        {pricingLabel}
                                    </LocalizedLink>
                                ) : null}
                            </div>
                        ) : null}
                        {shareLabel ? (
                            <ServiceShareButtons
                                locale={locale}
                                service={service}
                                shareLabel={shareLabel}
                            />
                        ) : null}
                    </div>
                    <div className="relative mx-auto w-full max-w-[600px] lg:justify-self-end">
                        <div
                            aria-hidden="true"
                            className="absolute inset-8 rounded-full bg-primary/20 blur-[100px]"
                        />
                        <div className="relative aspect-[14/9] overflow-hidden rounded-2xl border border-white/10 bg-surface-container-lowest/70 p-1 shadow-2xl">
                            <Image
                                alt={service.name}
                                className="h-full w-full rounded-xl object-contain"
                                height={900}
                                priority
                                sizes="(max-width: 1023px) 100vw, (max-width: 1440px) 42vw, 600px"
                                src={service.heroImage}
                                unoptimized={service.heroImage.endsWith(".svg")}
                                width={1400}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
