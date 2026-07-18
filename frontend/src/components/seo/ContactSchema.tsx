import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl, SITE_NAME } from "@/lib/seo";
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
    const contactPoint =
        contact.phone || contact.email
            ? [
                  {
                      "@type": "ContactPoint",
                      contactType: "sales",
                      ...(contact.phone ? { telephone: contact.phone } : {}),
                      ...(contact.email ? { email: contact.email } : {}),
                      availableLanguage: ["ka", "en", "ru"],
                  },
              ]
            : undefined;

    const mainEntity: Record<string, unknown> = {
        "@type": "Organization",
        name: siteName,
        url: absoluteLocalizedUrl("/", locale),
        logo: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        ...(description ? { description } : {}),
        ...(contact.phone ? { telephone: contact.phone } : {}),
        ...(contact.email ? { email: contact.email } : {}),
        ...(contact.address
            ? {
                  address: {
                      "@type": "PostalAddress",
                      streetAddress: contact.address,
                      addressCountry: "GE",
                      addressLocality: "Tbilisi",
                  },
              }
            : {}),
        ...(contactPoint ? { contactPoint } : {}),
    };

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
