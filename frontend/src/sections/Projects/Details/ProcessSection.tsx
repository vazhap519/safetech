import ProcessStep from "@/components/Projects/Details/ProcessStep";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProcessSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "project.detail.process.title",
        locale,
        null,
    );
    const stepLabel = translateText(
        translations,
        "project.detail.process.stepLabel",
        locale,
        null,
    );
    const steps = project.process.filter((step) => step.title || step.description);

    if (!steps.length) return null;

    return (
        <section
            aria-labelledby={title ? "implementation-title" : undefined}
            className="overflow-hidden py-12 sm:py-unit-xl"
        >
            <div className="mx-auto max-w-container-max px-4 sm:px-6 lg:px-margin-desktop">
                {title ? (
                    <h2
                        className="mb-7 text-[30px] font-semibold leading-tight sm:mb-12 sm:font-headline-xl sm:text-headline-xl"
                        id="implementation-title"
                    >
                        {title}
                    </h2>
                ) : null}
                <ol className="scrollbar-hide -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-5 sm:-mx-6 sm:gap-unit-md sm:px-6 lg:mx-0 lg:px-0">
                    {steps.map((step, index) => (
                        <ProcessStep
                            description={step.description}
                            index={index}
                            key={`${step.title}-${step.description}`}
                            last={index === steps.length - 1}
                            stepLabel={stepLabel}
                            title={step.title}
                        />
                    ))}
                </ol>
            </div>
        </section>
    );
}
