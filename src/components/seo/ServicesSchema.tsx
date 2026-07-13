import { getBackendServices } from "@/lib/backend";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function ServicesSchema() {
    const [{ branding, locale, translations }, services] = await Promise.all([
        getSiteSettings(),
        getBackendServices(),
    ]);
    const t = createTranslator(translations, locale);

    const schema = {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        name: t("meta.services.title", {
            ka: "CCTV, ქსელები და IT სერვისები | SafeTech",
            en: "CCTV, Networking, and IT Services | SafeTech",
            ru: "CCTV, сети и IT-услуги | SafeTech",
        }),
        description: t("meta.services.description", {
            ka: "იხილეთ SafeTech-ის CCTV, დაშვების კონტროლის, ქსელური, სერვერული და სხვა IT ინფრასტრუქტურული სერვისები.",
            en: "Explore SafeTech CCTV, access control, networking, server, and other IT infrastructure services.",
            ru: "Изучите услуги SafeTech: видеонаблюдение, контроль доступа, сети, серверы и другая IT-инфраструктура.",
        }),
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
                    description: service.description,
                    provider: {
                        "@type": "Organization",
                        name: branding.siteName,
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
