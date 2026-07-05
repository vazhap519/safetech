import { getBackendServices } from "@/lib/backend";
import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ServicesSchema() {
    const [{ branding }, services] = await Promise.all([
        getSiteSettings(),
        getBackendServices(),
    ]);

    const schema = {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        name: `${branding.siteName}-ის სერვისები`,
        description:
            "CCTV, დაშვების კონტროლი, ქსელები, სერვერები და IT ინფრასტრუქტურული გადაწყვეტილებები.",
        url: absoluteSiteUrl("/services"),
        inLanguage: "ka-GE",
        mainEntity: {
            "@type": "ItemList",
            itemListElement: services.map((service, index) => ({
                "@type": "ListItem",
                position: index + 1,
                name: service.title,
                item: {
                    "@type": "Service",
                    name: service.title,
                    description: service.description,
                    provider: {
                        "@type": "Organization",
                        name: branding.siteName,
                        url: absoluteSiteUrl("/"),
                    },
                    url: absoluteSiteUrl(`/services/${service.slug}`),
                },
            })),
        },
    };

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
