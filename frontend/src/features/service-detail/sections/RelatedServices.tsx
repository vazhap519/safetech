import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
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
    const related = service.related.filter(
        (item) => item.title || item.description,
    );
    const title = translateText(
        translations,
        "service.detail.related.title",
        locale,
        null,
    );
    const detailsLabel = translateText(
        translations,
        "common.readMore",
        locale,
        { ka: "დეტალურად", en: "View details", ru: "Подробнее" },
    );

    const linkedServices = related.filter((item) => item.slug);

    if (!linkedServices.length) return null;

    return (
        <section
            aria-labelledby={title ? "related-title" : undefined}
            className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl"
        >
            <div id="related-title">
                <SectionHeading>{title}</SectionHeading>
            </div>
            <div className="grid grid-cols-1 gap-unit-md md:grid-cols-3">
                {linkedServices.map((item) => (
                    <LocalizedLink
                        className="glass-card group flex flex-col gap-unit-sm rounded-lg p-unit-lg hover:border-primary"
                        href={`/services/${item.slug}`}
                        key={item.slug}
                    >
                        <Icon
                            className="text-primary transition-transform group-hover:scale-110"
                            name={item.icon}
                        />
                        {item.title ? (
                            <h3 className="font-headline-md text-xl text-white">
                                {item.title}
                            </h3>
                        ) : null}
                        {item.description ? (
                            <p className="flex-1 leading-relaxed text-on-surface-variant">
                                {item.description}
                            </p>
                        ) : null}
                        {detailsLabel ? (
                            <span className={`${CARD_ACTION_CLASS} mt-2 group-hover:border-secondary/50 group-hover:bg-secondary/10 group-hover:text-secondary`}>
                                {detailsLabel}
                                <Icon
                                    aria-hidden="true"
                                    className="text-[18px] transition-transform group-hover:translate-x-1"
                                    name="arrow_forward"
                                />
                            </span>
                        ) : null}
                    </LocalizedLink>
                ))}
            </div>
        </section>
    );
}
