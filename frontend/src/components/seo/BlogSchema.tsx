import JsonLd from "@/components/seo/JsonLd";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function BlogSchema() {
    const settings = await getSiteSettings();
    const name = translateText(settings.translations, "blog.title", settings.locale, {
        ka: "SafeTech ბლოგი",
        en: "SafeTech Blog",
        ru: "Блог SafeTech",
    });
    const schema = {
        "@context": "https://schema.org",
        "@type": "Blog",
        name,
        url: absoluteLocalizedUrl("/blog", settings.locale),
        inLanguage: settings.locale,
        publisher: {
            "@type": "Organization",
            "@id": `${absoluteSiteUrl("/")}#organization`,
            name: settings.branding.siteName,
        },
    };

    return <JsonLd data={schema} />;
}
