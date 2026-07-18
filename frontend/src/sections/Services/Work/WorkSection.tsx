import StepComponent from "@/components/Service/Work/StepComponent";
import WorkTypographyComponent from "@/components/Service/Work/WorkTypographyComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const stepIndexes = [0, 1, 2, 3, 4, 5];

export default async function WorkSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "services.work.title", locale, null);
    const steps = stepIndexes
        .map((index) => ({
            description: translateText(
                translations,
                `services.work.step.${index}.description`,
                locale,
                null,
            ),
            index: index + 1,
            title: translateText(
                translations,
                `services.work.step.${index}.title`,
                locale,
                null,
            ),
        }))
        .filter((step) => step.title || step.description);

    if (!title && !steps.length) return null;

    return (
        <section className="relative overflow-hidden bg-surface-container-low/30 py-unit-xl">
            <div className="ambient-glow -bottom-40 -right-40 opacity-30" />
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title ? <WorkTypographyComponent title={title} /> : null}
                {steps.length ? (
                    <div className="relative">
                        <div className="timeline-line absolute left-0 top-12 hidden h-1 w-full opacity-20 lg:block" />
                        <div className="relative grid grid-cols-1 gap-unit-lg sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                            {steps.map((step) => (
                                <StepComponent
                                    description={step.description}
                                    index={step.index}
                                    key={`work-step-${step.index}`}
                                    title={step.title}
                                />
                            ))}
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
