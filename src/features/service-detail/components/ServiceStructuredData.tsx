import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

import type { ServiceDetail } from "../model/types";

export default async function ServiceStructuredData({
    service,
}: {
    service: ServiceDetail;
}) {
    const { contact, branding } = await getSiteSettings();
    const url = absoluteSiteUrl(`/services/${service.slug}`);
    const graph: Array<Record<string, unknown>> = [
        {
            "@type": "Service",
            "@id": `${url}#service`,
            name: service.name,
            description: service.seoDescription,
            url,
            serviceType: service.name,
            image: absoluteSiteUrl(service.heroImage || branding.defaultImage),
            provider: {
                "@type": "LocalBusiness",
                "@id": `${absoluteSiteUrl("/")}#organization`,
                name: branding.siteName,
                url: absoluteSiteUrl("/"),
                telephone: contact.phone,
                email: contact.email,
                logo: absoluteSiteUrl(
                    branding.logo || branding.footerLogo || branding.defaultImage,
                ),
            },
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
                    name: "მთავარი",
                    item: absoluteSiteUrl("/"),
                },
                {
                    "@type": "ListItem",
                    position: 2,
                    name: "სერვისები",
                    item: absoluteSiteUrl("/services"),
                },
                {
                    "@type": "ListItem",
                    position: 3,
                    name: service.name,
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
