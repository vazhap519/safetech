import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectCtaSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "project.detail.cta.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "project.detail.cta.description",
        locale,
        null,
    );
    const primaryLabel = translateText(
        translations,
        "project.detail.cta.primary",
        locale,
        null,
    );
    const secondaryLabel = translateText(
        translations,
        "project.detail.cta.secondary",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: primaryLabel,
                    href: "/contact",
                    className:
                        "rounded-2xl px-8 py-5 font-label-md transition-transform motion-safe:hover:scale-105 motion-safe:hover:translate-y-0",
                },
                {
                    label: secondaryLabel,
                    href: "/contact",
                    variant: "outline",
                    className: "px-8 py-5 font-label-md",
                },
            ]}
            actionsClassName="relative flex flex-col justify-center gap-unit-md sm:flex-row"
            description={description}
            descriptionClassName="relative mx-auto mb-12 max-w-2xl text-body-lg leading-relaxed text-on-surface-variant"
            panelBackground={
                <div
                    aria-hidden="true"
                    className="ambient-glow -top-1/2 left-1/2 h-[600px] w-[min(600px,140vw)] -translate-x-1/2 bg-primary-container"
                />
            }
            panelClassName="glass-card relative mx-auto max-w-container-max overflow-hidden rounded-3xl border-primary-container p-unit-lg text-center sm:p-unit-xl"
            sectionClassName="relative px-margin-desktop py-unit-xl"
            title={title}
            titleClassName="relative mb-8 font-display-lg text-display-lg-mobile leading-tight md:text-display-lg"
        />
    );
}
