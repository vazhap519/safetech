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
            className="overflow-hidden py-unit-xl"
        >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title ? (
                    <h2
                        className="mb-12 font-headline-xl text-headline-xl"
                        id="implementation-title"
                    >
                        {title}
                    </h2>
                ) : null}
                <ol className="scrollbar-hide flex snap-x gap-unit-md overflow-x-auto pb-8">
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
