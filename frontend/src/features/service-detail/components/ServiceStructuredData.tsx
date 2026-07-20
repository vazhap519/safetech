import JsonLd from "@/components/seo/JsonLd";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { buildBreadcrumbSchema } from "@/lib/structured-data";
import { createTranslator } from "@/lib/translations";

import type { ServiceDetail } from "../model/types";

export default async function ServiceStructuredData({
    service,
}: {
    service: ServiceDetail;
}) {
    const { contact, branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const url = absoluteLocalizedUrl(`/services/${service.slug}`, locale);
    const provider: Record<string, unknown> = {
        "@type": "LocalBusiness",
        "@id": `${absoluteSiteUrl("/")}#organization`,
        name: branding.siteName,
        url: absoluteLocalizedUrl("/", locale),
        logo: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        ...(contact.phone ? { telephone: contact.phone } : {}),
        ...(contact.email ? { email: contact.email } : {}),
    };

    const graph: Array<Record<string, unknown>> = [
        {
            "@type": "Service",
            "@id": `${url}#service`,
            name: service.title || service.name,
            description: service.seoDescription,
            url,
            serviceType: service.name,
            image: absoluteSiteUrl(service.heroImage || branding.defaultImage),
            inLanguage: getLanguageTag(locale),
            provider,
            areaServed: {
                "@type": "Country",
                name: "Georgia",
            },
        },
        buildBreadcrumbSchema([
            {
                name: t("nav.home", {
                        ka: "მთავარი",
                        en: "Home",
                        ru: "Главная",
                    }),
                url: absoluteLocalizedUrl("/", locale),
            },
            {
                name: t("nav.services", {
                        ka: "სერვისები",
                        en: "Services",
                        ru: "Услуги",
                    }),
                url: absoluteLocalizedUrl("/services", locale),
            },
            {
                name: service.title || service.name,
                url,
            },
        ]),
    ];

    if (service.faqs.length) {
        graph.push({
            "@type": "FAQPage",
            mainEntity: service.faqs.map((faq) => ({
                "@type": "Question",
                name: faq.question,
                acceptedAnswer: {
                    "@type": "Answer",
                    text: faq.answer,
                },
            })),
        });
    }

    if (service.seo?.schema) {
        return <JsonLd data={service.seo.schema} />;
    }

    return (
        <JsonLd
            data={{
                "@context": "https://schema.org",
                "@graph": graph,
            }}
        />
    );
}
