import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl, SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HomeSchema() {
    const { branding, locale, socialLinks, translations } =
        await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const description = translateText(
        translations,
        "meta.home.description",
        locale,
        null,
    );
    const publisher: Record<string, unknown> = {
        "@type": "Organization",
        name: siteName,
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
        name: siteName,
        url: absoluteLocalizedUrl("/", locale),
        inLanguage: getLanguageTag(locale),
        ...(description ? { description } : {}),
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
