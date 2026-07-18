import HowComponent from "@/components/About/HowComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const stepIndexes = [0, 1, 2, 3];

export default async function HowSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.how.title", locale, null);
    const steps = stepIndexes
        .map((index) => ({
            title: translateText(
                translations,
                `about.how.item.${index}.title`,
                locale,
                null,
            ),
            description: translateText(
                translations,
                `about.how.item.${index}.description`,
                locale,
                null,
            ),
            align: index % 2 === 0 ? ("right" as const) : ("left" as const),
        }))
        .filter((step) => step.title || step.description);

    if (!title && !steps.length) return null;

    return (
        <section className="relative overflow-hidden py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title ? (
                    <h2 className="mb-unit-xl text-center font-headline-xl text-headline-xl text-on-surface">
                        {title}
                    </h2>
                ) : null}
                {steps.length ? (
                    <div className="relative">
                        <div className="absolute bottom-0 left-1/2 top-0 hidden w-px bg-outline-variant/30 md:block" />
                        <div className="relative space-y-unit-lg">
                            {steps.map((step, index) => (
                                <HowComponent
                                    key={`${step.title}-${index}`}
                                    {...step}
                                />
                            ))}
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
