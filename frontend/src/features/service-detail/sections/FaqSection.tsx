import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import FaqAccordion from "../components/FaqAccordion";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function FaqSection({ service }: { service: ServiceDetail }) {
    const { locale, translations } = await getSiteSettings();
    const faqs = service.faqs.filter((faq) => faq.question || faq.answer);
    const eyebrow = translateText(
        translations,
        "service.detail.faq.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "service.detail.faq.title",
        locale,
        null,
    );

    if (!faqs.length) return null;

    return (
        <section
            aria-labelledby={title ? "faq-title" : undefined}
            className="scroll-reveal mx-auto max-w-4xl px-margin-desktop py-unit-xl"
        >
            <div id="faq-title">
                <SectionHeading centered eyebrow={eyebrow}>
                    {title}
                </SectionHeading>
            </div>
            <FaqAccordion items={faqs} />
        </section>
    );
}
