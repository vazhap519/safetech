import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function CtaSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.cta.title", locale, null);
    const description = translateText(
        translations,
        "about.cta.description",
        locale,
        null,
    );
    const buttonLabel = translateText(
        translations,
        "about.cta.button",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: buttonLabel,
                    href: "/contact",
                    className: "px-8 py-4",
                },
            ]}
            actionsClassName="mt-unit-lg flex justify-center"
            description={description}
            descriptionVariant="section-description"
            panelClassName="glass-card relative z-10 mx-auto max-w-container-max rounded-3xl border-primary/20 px-unit-lg py-unit-xl text-center sm:px-unit-xl"
            sectionBackground={
                <div className="mesh-gradient absolute inset-0 opacity-30" />
            }
            sectionClassName="relative overflow-hidden px-margin-desktop py-unit-xl"
            title={title}
            titleVariant="section-title"
        />
    );
}
