import { getLanguageTag } from "@/lib/locales";
import { buildOrganizationEntity } from "@/lib/organization-schema";
import { absoluteLocalizedUrl, SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ContactSchema() {
    const { contact, branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const description = translateText(
        translations,
        "meta.contact.description",
        locale,
        null,
    );
    const pageName =
        translateText(translations, "meta.contact.title", locale, null) ||
        siteName;
    const mainEntity = buildOrganizationEntity({
        branding,
        contact,
        contactType: "sales",
        description,
        siteName,
        url: absoluteLocalizedUrl("/", locale),
    });

    const schema = {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        name: pageName,
        url: absoluteLocalizedUrl("/contact", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity,
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
