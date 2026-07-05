import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function HomeSchema() {
    const { branding } = await getSiteSettings();
    const schema = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        name: branding.siteName,
        url: absoluteSiteUrl("/"),
        publisher: {
            "@type": "Organization",
            name: branding.siteName,
            url: absoluteSiteUrl("/"),
            logo: branding.logo || branding.footerLogo || branding.defaultImage,
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
