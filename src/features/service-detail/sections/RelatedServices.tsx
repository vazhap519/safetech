import Link from "next/link";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default function RelatedServices({ service }: { service: ServiceDetail }) {
    return (
        <section className="scroll-reveal bg-surface-container-low px-margin-desktop py-unit-xl" aria-labelledby="related-title">
            <div id="related-title"><SectionHeading>მსგავსი სერვისები</SectionHeading></div>
            <div className="grid grid-cols-1 gap-unit-md md:grid-cols-3">
                {service.related.map((item) => (
                    <Link className="glass-card group flex flex-col gap-unit-sm rounded-2xl p-unit-lg hover:border-primary" href={`/services/${item.slug}`} key={item.slug}>
                        <span aria-hidden="true" className="material-symbols-outlined text-primary transition-transform group-hover:scale-110">{item.icon}</span>
                        <h3 className="font-headline-md text-xl text-white">{item.title}</h3>
                        <p className="leading-relaxed text-on-surface-variant">{item.description}</p>
                    </Link>
                ))}
            </div>
        </section>
    );
}
