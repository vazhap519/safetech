import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function BenefitsSection({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const benefits = service.benefits.filter(
        (benefit) => benefit.title || benefit.description,
    );
    const eyebrow = translateText(
        translations,
        "service.detail.benefits.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "service.detail.benefits.title",
        locale,
        null,
    );

    if (!benefits.length) return null;

    return (
        <section
            aria-labelledby={title ? "benefits-title" : undefined}
            className="scroll-reveal px-margin-desktop py-unit-xl"
        >
            <div id="benefits-title">
                <SectionHeading centered eyebrow={eyebrow}>
                    {title}
                </SectionHeading>
            </div>
            <div className="grid grid-cols-1 gap-gutter md:grid-cols-3">
                {benefits.map((benefit) => (
                    <article
                        className="glass-card flex flex-col gap-unit-md rounded-2xl p-unit-lg"
                        key={benefit.title || benefit.icon}
                    >
                        <Icon
                            className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/20 p-3 text-primary"
                            name={benefit.icon}
                        />
                        {benefit.title ? (
                            <h3 className="font-headline-md text-xl text-white">
                                {benefit.title}
                            </h3>
                        ) : null}
                        {benefit.description ? (
                            <p className="leading-relaxed text-on-surface-variant">
                                {benefit.description}
                            </p>
                        ) : null}
                    </article>
                ))}
            </div>
        </section>
    );
}
