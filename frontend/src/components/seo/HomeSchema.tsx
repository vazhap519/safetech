import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function HomeSchema() {
    const { branding, locale, socialLinks, translations } =
        await getSiteSettings();
    const t = createTranslator(translations, locale);
    const publisher: Record<string, unknown> = {
        "@type": "Organization",
        name: branding.siteName,
        url: absoluteLocalizedUrl("/", locale),
        logo: absoluteSiteUrl(
            branding.logo || branding.footerLogo || branding.defaultImage,
        ),
        ...(socialLinks.length
            ? { sameAs: socialLinks.map((item) => item.href) }
            : {}),
    };
    const schema = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        name: branding.siteName,
        url: absoluteLocalizedUrl("/", locale),
        inLanguage: getLanguageTag(locale),
        description: t("meta.home.description", {
            ka: "SafeTech უზრუნველყოფს ვიდეომეთვალყურეობას, დაშვების კონტროლს, ქსელურ და სერვერულ ინფრასტრუქტურას საქართველოში.",
            en: "SafeTech delivers CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.",
            ru: "SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.",
        }),
        publisher,
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
