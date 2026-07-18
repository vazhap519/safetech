import CmsCtaSection from "@/components/cta/CmsCtaSection";
import { toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

import type { ServiceDetail } from "../model/types";

export default async function ServiceCta({
    service,
}: {
    service: ServiceDetail;
}) {
    const { contact, locale, translations } = await getSiteSettings();
    const titlePrefix = translateText(
        translations,
        "service.detail.cta.titlePrefix",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "service.detail.cta.description",
        locale,
        null,
    );
    const consultationLabel = translateText(
        translations,
        "service.detail.cta.consultation",
        locale,
        null,
    );
    const callLabel = translateText(
        translations,
        "service.detail.cta.call",
        locale,
        null,
    );

    return (
        <CmsCtaSection
            actions={[
                {
                    label: consultationLabel,
                    consultation: true,
                    className:
                        "glow-button px-unit-xl py-unit-md font-headline-md hover:shadow-[0_0_30px_rgba(37,99,235,0.4)]",
                },
                contact.phone
                    ? {
                          label: callLabel,
                          href: toPhoneHref(contact.phone),
                          variant: "secondary",
                          className:
                              "rounded-xl border-outline-variant bg-white/5 px-unit-xl py-unit-md font-headline-md text-white hover:bg-white/10",
                      }
                    : {},
            ]}
            actionsClassName="flex flex-wrap justify-center gap-unit-md"
            description={description}
            descriptionClassName="mx-auto max-w-2xl text-lg leading-relaxed text-on-surface-variant"
            panelClassName="glass-card space-y-unit-lg rounded-3xl p-unit-lg text-center sm:rounded-[40px] sm:p-unit-xl"
            sectionBackground={
                <div aria-hidden="true" className="ambient-glow bottom-0 right-0" />
            }
            sectionClassName="relative overflow-hidden px-margin-desktop py-unit-xl"
            title={titlePrefix ? `${titlePrefix} ${service.name}` : null}
            titleClassName="font-display-lg text-[34px] leading-tight text-white sm:text-[46px] md:text-[58px]"
            titleId="service-cta-title"
        />
    );
}
