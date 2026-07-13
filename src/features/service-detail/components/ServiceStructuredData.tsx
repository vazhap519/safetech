import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
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
        {
            "@type": "BreadcrumbList",
            itemListElement: [
                {
                    "@type": "ListItem",
                    position: 1,
                    name: t("nav.home", {
                        ka: "მთავარი",
                        en: "Home",
                        ru: "Главная",
                    }),
                    item: absoluteLocalizedUrl("/", locale),
                },
                {
                    "@type": "ListItem",
                    position: 2,
                    name: t("nav.services", {
                        ka: "სერვისები",
                        en: "Services",
                        ru: "Услуги",
                    }),
                    item: absoluteLocalizedUrl("/services", locale),
                },
                {
                    "@type": "ListItem",
                    position: 3,
                    name: service.title || service.name,
                    item: url,
                },
            ],
        },
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

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify({
                    "@context": "https://schema.org",
                    "@graph": graph,
                }).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
