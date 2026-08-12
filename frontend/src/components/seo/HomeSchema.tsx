import { getLanguageTag } from "@/lib/locales";
import {
    absoluteLocalizedUrl,
    absoluteSiteUrl,
    SITE_NAME,
} from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HomeSchema() {
    const { branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName || SITE_NAME;
    const homeUrl = absoluteLocalizedUrl("/", locale);
    const organizationId = `${absoluteSiteUrl("/")}#organization`;
    const description = translateText(
        translations,
        "meta.home.description",
        locale,
        null,
    );
    const schema = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "@id": `${homeUrl}#website`,
        name: siteName,
        url: homeUrl,
        inLanguage: getLanguageTag(locale),
        ...(description ? { description } : {}),
        publisher: {
            "@id": organizationId,
        },
        about: {
            "@id": organizationId,
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
