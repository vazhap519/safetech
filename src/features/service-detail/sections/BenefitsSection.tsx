import Icon from "@/components/ui/Icon";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default function BenefitsSection({ service }: { service: ServiceDetail }) {
    return (
        <section className="scroll-reveal px-margin-desktop py-unit-xl" aria-labelledby="benefits-title">
            <div id="benefits-title"><SectionHeading centered eyebrow="SafeTech-ის უპირატესობები">რატომ უნდა აგვირჩიოთ?</SectionHeading></div>
            <div className="grid grid-cols-1 gap-gutter md:grid-cols-3">
                {service.benefits.map((benefit) => (
                    <article className="glass-card flex flex-col gap-unit-md rounded-2xl p-unit-lg" key={benefit.title}>
                        <Icon
                            className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/20 p-3 text-primary"
                            name={benefit.icon}
                        />
                        <h3 className="font-headline-md text-xl text-white">{benefit.title}</h3>
                        <p className="leading-relaxed text-on-surface-variant">{benefit.description}</p>
                    </article>
                ))}
            </div>
        </section>
    );
}
