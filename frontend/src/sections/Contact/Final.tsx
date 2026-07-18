import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FinalSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "contact.final.title", locale, null);
    const buttonLabel = translateText(
        translations,
        "contact.final.button",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: buttonLabel,
                    href: "#contact-form",
                    className: "w-full max-w-xs sm:w-auto sm:max-w-none",
                },
            ]}
            actionsClassName="mt-6 flex w-full justify-center md:mt-8"
            contentClassName="mx-auto flex max-w-container-max flex-col items-center justify-center px-4 sm:px-6 lg:px-margin-desktop"
            panelClassName="contents"
            sectionClassName="bg-gradient-to-b from-surface-container-low to-background py-16 sm:py-20 md:py-24 lg:py-unit-xl"
            title={title}
            titleClassName="mx-auto max-w-5xl text-balance text-center"
            titleVariant="contact-final-cta"
        />
    );
}
