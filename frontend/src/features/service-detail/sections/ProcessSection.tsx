import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import SectionHeading from "../components/SectionHeading";
import type { ServiceDetail } from "../model/types";

export default async function ProcessSection({ service }: { service: ServiceDetail }) {
    const { locale, translations } = await getSiteSettings();

    return (
        <section className="scroll-reveal px-margin-desktop py-unit-xl" aria-labelledby="process-title">
            <div id="process-title"><SectionHeading eyebrow={translateText(translations, "service.detail.process.eyebrow", locale, {
                ka: "გამჭვირვალე პროცესი",
                en: "Transparent process",
                ru: "Прозрачный процесс",
            })}>{translateText(translations, "service.detail.process.title", locale, {
                ka: "როგორ ვმუშაობთ",
                en: "How we work",
                ru: "Как мы работаем",
            })}</SectionHeading></div>
            <ol className="relative space-y-unit-lg before:absolute before:bottom-8 before:left-8 before:top-8 before:hidden before:w-px before:bg-primary/20 md:before:block">
                {service.process.map((step, index) => (
                    <li className="relative flex flex-col items-start gap-unit-lg md:flex-row" key={step.title}>
                        <span className={`${index === 0 ? "bg-primary text-on-primary" : "border border-primary bg-primary/20 text-primary"} z-10 flex h-16 w-16 shrink-0 items-center justify-center rounded-full text-xl font-bold`} aria-hidden="true">{index + 1}</span>
                        <article className="glass-card flex-1 rounded-2xl p-unit-lg">
                            <h3 className="mb-unit-xs font-headline-md text-xl text-white">{step.title}</h3>
                            <p className="leading-relaxed text-on-surface-variant">{step.description}</p>
                        </article>
                    </li>
                ))}
            </ol>
        </section>
    );
}
