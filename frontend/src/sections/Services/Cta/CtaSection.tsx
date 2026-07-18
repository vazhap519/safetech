import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function CtaSection() {
    const { contact, locale, translations } = await getSiteSettings();
    const title = translateText(translations, "services.cta.title", locale, null);
    const description = translateText(
        translations,
        "services.cta.description",
        locale,
        null,
    );
    const quoteLabel = translateText(
        translations,
        "services.cta.quote",
        locale,
        null,
    );
    const callLabel = translateText(
        translations,
        "services.cta.call",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: quoteLabel,
                    href: "/contact",
                    className: "px-unit-xl py-unit-md text-lg",
                },
                contact.phone
                    ? {
                          label: callLabel ? `${callLabel}: ${contact.phone}` : null,
                          href: toPhoneHref(contact.phone),
                          variant: "glass",
                          className: "px-unit-xl py-unit-md text-lg",
                      }
                    : {},
            ]}
            actionsClassName="flex flex-col justify-center gap-unit-md sm:flex-row"
            contentClassName="relative z-10"
            description={description}
            descriptionClassName="mx-auto mb-unit-lg max-w-2xl font-body-lg text-body-lg text-on-surface-variant"
            panelBackground={
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-transparent" />
            }
            panelClassName="glass-card relative overflow-hidden rounded-3xl border-primary/20 bg-primary-container/10 p-unit-lg text-center sm:p-unit-xl"
            sectionClassName="mx-auto mb-unit-xl max-w-container-max px-margin-desktop py-unit-xl"
            title={title}
            titleClassName="mb-unit-md font-headline-xl text-headline-xl"
        />
    );
}
