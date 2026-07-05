import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default function SolutionsSection({ service }: { service: ServiceDetail }) {
    return (
        <section className="scroll-reveal px-margin-desktop py-unit-xl" aria-labelledby="solutions-title">
            <div id="solutions-title"><SectionHeading eyebrow="მორგებული თქვენს საჭიროებებზე">სპეციალიზებული გადაწყვეტილებები</SectionHeading></div>
            <div className="grid grid-cols-1 gap-unit-md md:grid-cols-12">
                {service.solutions.map((solution, index) => (
                    <article
                        className={`${solution.featured ? "md:col-span-6 md:min-h-72" : "md:col-span-3"} glass-card group relative flex min-h-52 flex-col justify-end overflow-hidden rounded-3xl p-unit-lg`}
                        key={solution.title}
                    >
                        {solution.featured && <div aria-hidden="true" className={`absolute inset-0 bg-gradient-to-br ${index % 2 ? "from-secondary/20" : "from-primary/20"} to-transparent opacity-70`} />}
                        <div className="relative z-10">
                            <span aria-hidden="true" className="material-symbols-outlined mb-unit-md text-3xl text-secondary">{solution.icon}</span>
                            <h3 className="mb-unit-xs font-headline-md text-xl text-white">{solution.title}</h3>
                            <p className="leading-relaxed text-on-surface-variant">{solution.description}</p>
                        </div>
                    </article>
                ))}
            </div>
        </section>
    );
}
