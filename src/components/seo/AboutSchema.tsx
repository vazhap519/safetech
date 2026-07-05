import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function AboutSchema() {
    const { branding } = await getSiteSettings();
    const schema = {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        name: `About ${branding.siteName}`,
        url: absoluteSiteUrl("/about"),
        mainEntity: {
            "@type": "Organization",
            name: branding.siteName,
            url: absoluteSiteUrl("/"),
            description:
                "IT ინფრასტრუქტურის, უსაფრთხოების სისტემებისა და ქსელური გადაწყვეტილებების კომპანია.",
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
