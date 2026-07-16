import FaqAccordion from "../components/FaqAccordion";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function FaqSection({ service }: { service: ServiceDetail }) {
    const { locale, translations } = await getSiteSettings();

    return (
        <section className="scroll-reveal mx-auto max-w-4xl px-margin-desktop py-unit-xl" aria-labelledby="faq-title">
            <div id="faq-title"><SectionHeading centered eyebrow={translateText(translations, "service.detail.faq.eyebrow", locale, {
                ka: "FAQ",
                en: "FAQ",
                ru: "FAQ",
            })}>{translateText(translations, "service.detail.faq.title", locale, {
                ka: "ხშირად დასმული კითხვები",
                en: "Frequently asked questions",
                ru: "Часто задаваемые вопросы",
            })}</SectionHeading></div>
            <FaqAccordion items={service.faqs} />
        </section>
    );
}
