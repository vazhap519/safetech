import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function SolutionsSection({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const solutions = service.solutions.filter(
        (solution) => solution.title || solution.description,
    );
    const eyebrow = translateText(
        translations,
        "service.detail.solutions.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "service.detail.solutions.title",
        locale,
        null,
    );

    if (!solutions.length) return null;

    return (
        <section
            aria-labelledby={title ? "solutions-title" : undefined}
            className="scroll-reveal px-margin-desktop py-unit-xl"
        >
            <div id="solutions-title">
                <SectionHeading eyebrow={eyebrow}>{title}</SectionHeading>
            </div>
            <div className="grid grid-cols-1 gap-unit-md md:grid-cols-12">
                {solutions.map((solution, index) => (
                    <article
                        className={`${
                            solution.featured
                                ? "md:col-span-6 md:min-h-72"
                                : "md:col-span-3"
                        } glass-card group relative flex min-h-52 flex-col justify-end overflow-hidden rounded-3xl p-unit-lg`}
                        key={solution.title || solution.icon}
                    >
                        {solution.featured ? (
                            <div
                                aria-hidden="true"
                                className={`absolute inset-0 bg-gradient-to-br ${
                                    index % 2 ? "from-secondary/20" : "from-primary/20"
                                } to-transparent opacity-70`}
                            />
                        ) : null}
                        <div className="relative z-10">
                            <Icon
                                className="mb-unit-md text-3xl text-secondary"
                                name={solution.icon}
                            />
                            {solution.title ? (
                                <h3 className="mb-unit-xs font-headline-md text-xl text-white">
                                    {solution.title}
                                </h3>
                            ) : null}
                            {solution.description ? (
                                <p className="leading-relaxed text-on-surface-variant">
                                    {solution.description}
                                </p>
                            ) : null}
                        </div>
                    </article>
                ))}
            </div>
        </section>
    );
}
