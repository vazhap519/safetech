import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl, SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function AboutSchema() {
    const { branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const pageName =
        translateText(translations, "meta.about.title", locale, null) ||
        siteName;
    const description = translateText(
        translations,
        "meta.about.description",
        locale,
        null,
    );
    const schema = {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        name: pageName,
        url: absoluteLocalizedUrl("/about", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity: {
            "@type": "Organization",
            name: siteName,
            url: absoluteLocalizedUrl("/", locale),
            ...(description ? { description } : {}),
            logo: absoluteSiteUrl(
                branding.logo || branding.footerLogo || branding.defaultImage,
            ),
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
