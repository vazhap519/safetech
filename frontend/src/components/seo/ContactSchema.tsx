import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function ContactSchema() {
    const { contact, branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
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
        name: branding.siteName,
        url: absoluteLocalizedUrl("/", locale),
        logo: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        description: t("meta.contact.description", {
            ka: "დაგვიკავშირდით CCTV, ქსელური, სერვერული და უსაფრთხოების სისტემების პროექტებისთვის საქართველოში.",
            en: "Contact SafeTech for CCTV, networking, server, and security system projects in Georgia.",
            ru: "Свяжитесь с SafeTech по проектам видеонаблюдения, сетевой, серверной и охранной инфраструктуры в Грузии.",
        }),
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
        name: t("meta.contact.title", {
            ka: "კონტაქტი და კონსულტაცია | SafeTech",
            en: "Contact and Consultation | SafeTech",
            ru: "Контакты и консультация | SafeTech",
        }),
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
