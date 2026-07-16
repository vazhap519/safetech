import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function IndustriesSection({ service }: { service: ServiceDetail }) {
    const { locale, translations } = await getSiteSettings();

    return (
        <section className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl" aria-labelledby="industries-title">
            <div id="industries-title"><SectionHeading centered>{translateText(translations, "service.detail.industries.title", locale, {
                ka: "ინდუსტრიები",
                en: "Industries",
                ru: "Отрасли",
            })}</SectionHeading></div>
            <ul className="flex flex-wrap justify-center gap-unit-md">
                {service.industries.map((industry) => <li className="glass-card rounded-full px-unit-xl py-unit-md font-headline-md text-lg text-white" key={industry}>{industry}</li>)}
            </ul>
        </section>
    );
}
