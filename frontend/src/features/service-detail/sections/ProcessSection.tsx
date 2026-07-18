import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function ProcessSection({
    service,
}: {
    service: ServiceDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const steps = service.process.filter((step) => step.title || step.description);
    const eyebrow = translateText(
        translations,
        "service.detail.process.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "service.detail.process.title",
        locale,
        null,
    );

    if (!steps.length) return null;

    return (
        <section
            aria-labelledby={title ? "process-title" : undefined}
            className="scroll-reveal px-margin-desktop py-unit-xl"
        >
            <div id="process-title">
                <SectionHeading eyebrow={eyebrow}>{title}</SectionHeading>
            </div>
            <ol className="relative space-y-unit-lg before:absolute before:bottom-8 before:left-8 before:top-8 before:hidden before:w-px before:bg-primary/20 md:before:block">
                {steps.map((step, index) => (
                    <li
                        className="relative flex flex-col items-start gap-unit-lg md:flex-row"
                        key={step.title || index}
                    >
                        <span
                            aria-hidden="true"
                            className={`${
                                index === 0
                                    ? "bg-primary text-on-primary"
                                    : "border border-primary bg-primary/20 text-primary"
                            } z-10 flex h-16 w-16 shrink-0 items-center justify-center rounded-full text-xl font-bold`}
                        >
                            {index + 1}
                        </span>
                        <article className="glass-card flex-1 rounded-2xl p-unit-lg">
                            {step.title ? (
                                <h3 className="mb-unit-xs font-headline-md text-xl text-white">
                                    {step.title}
                                </h3>
                            ) : null}
                            {step.description ? (
                                <p className="leading-relaxed text-on-surface-variant">
                                    {step.description}
                                </p>
                            ) : null}
                        </article>
                    </li>
                ))}
            </ol>
        </section>
    );
}
