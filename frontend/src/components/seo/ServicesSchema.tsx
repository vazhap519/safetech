import { getBackendServices } from "@/lib/backend";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServicesSchema() {
    const [{ branding, locale, translations }, services] = await Promise.all([
        getSiteSettings(),
        getBackendServices(),
    ]);
    const siteName = branding.siteName || SITE_NAME;
    const name =
        translateText(translations, "meta.services.title", locale, null) ||
        siteName;
    const description = translateText(
        translations,
        "meta.services.description",
        locale,
        null,
    );

    const schema = {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        name,
        ...(description ? { description } : {}),
        url: absoluteLocalizedUrl("/services", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity: {
            "@type": "ItemList",
            itemListElement: services.map((service, index) => ({
                "@type": "ListItem",
                position: index + 1,
                name: service.title,
                item: {
                    "@type": "Service",
                    name: service.title,
                    ...(service.description
                        ? { description: service.description }
                        : {}),
                    provider: {
                        "@type": "Organization",
                        name: siteName,
                        url: absoluteLocalizedUrl("/", locale),
                    },
                    url: absoluteLocalizedUrl(`/services/${service.slug}`, locale),
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
