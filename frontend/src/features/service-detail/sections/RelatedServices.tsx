import LocalizedLink from "@/components/ui/LocalizedLink";
import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function RelatedServices({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();

    return (
        <section
            className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl"
            aria-labelledby="related-title"
        >
            <div id="related-title">
                <SectionHeading>{translateText(translations, "service.detail.related.title", locale, {
                    ka: "მსგავსი სერვისები",
                    en: "Related services",
                    ru: "Похожие услуги",
                })}</SectionHeading>
            </div>
            <div className="grid grid-cols-1 gap-unit-md md:grid-cols-3">
                {service.related.map((item) => (
                    <LocalizedLink
                        className="glass-card group flex flex-col gap-unit-sm rounded-2xl p-unit-lg hover:border-primary"
                        href={`/services/${item.slug}`}
                        key={item.slug}
                    >
                        <Icon
                            className="text-primary transition-transform group-hover:scale-110"
                            name={item.icon}
                        />
                        <h3 className="font-headline-md text-xl text-white">
                            {item.title}
                        </h3>
                        <p className="leading-relaxed text-on-surface-variant">
                            {item.description}
                        </p>
                    </LocalizedLink>
                ))}
            </div>
        </section>
    );
}
