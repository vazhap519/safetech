import FaqAccordion from "../components/FaqAccordion";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default function FaqSection({ service }: { service: ServiceDetail }) {
    return (
        <section className="scroll-reveal mx-auto max-w-4xl px-margin-desktop py-unit-xl" aria-labelledby="faq-title">
            <div id="faq-title"><SectionHeading centered eyebrow="FAQ">ხშირად დასმული კითხვები</SectionHeading></div>
            <FaqAccordion items={service.faqs} />
        </section>
    );
}
