import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function IndustriesSection({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const industries = service.industries.filter(Boolean);
    const title = translateText(
        translations,
        "service.detail.industries.title",
        locale,
        null,
    );

    if (!industries.length) return null;

    return (
        <section
            aria-labelledby={title ? "industries-title" : undefined}
            className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl"
        >
            <div id="industries-title">
                <SectionHeading centered>{title}</SectionHeading>
            </div>
            <ul className="flex flex-wrap justify-center gap-unit-md">
                {industries.map((industry) => (
                    <li
                        className="glass-card rounded-full px-unit-xl py-unit-md font-headline-md text-lg text-white"
                        key={industry}
                    >
                        {industry}
                    </li>
                ))}
            </ul>
        </section>
    );
}
