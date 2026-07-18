import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectsCtaSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "projects.cta.title", locale, null);
    const description = translateText(
        translations,
        "projects.cta.description",
        locale,
        null,
    );
    const buttonLabel = translateText(
        translations,
        "projects.cta.button",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: buttonLabel,
                    consultation: true,
                    className:
                        "rounded-full px-unit-lg py-4 font-label-md font-bold transition-transform motion-safe:hover:scale-105 motion-safe:hover:translate-y-0",
                },
            ]}
            actionsClassName="flex justify-center"
            afterPanel={
                <div
                    aria-hidden="true"
                    className="ambient-glow absolute left-1/2 top-1/2 z-0 h-[600px] w-[min(600px,140vw)] -translate-x-1/2 -translate-y-1/2"
                />
            }
            description={description}
            descriptionClassName="mx-auto mb-unit-lg max-w-2xl font-body-lg text-body-lg leading-relaxed text-on-surface-variant"
            panelClassName="glass-card relative z-10 mx-auto max-w-4xl rounded-2xl px-unit-lg py-unit-xl text-center sm:px-unit-xl"
            sectionClassName="relative overflow-hidden px-margin-desktop py-unit-xl"
            title={title}
            titleClassName="mb-6 font-display-lg text-display-lg-mobile text-white md:text-headline-xl"
            titleId="projects-cta-title"
        />
    );
}
