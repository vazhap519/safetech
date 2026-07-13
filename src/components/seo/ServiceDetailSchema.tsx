import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ServiceDetailSchema({
    name,
    description,
    url,
}: {
    name: string;
    description: string;
    url: string;
}) {
    const { branding, contact } = await getSiteSettings();
    const provider: Record<string, unknown> = {
        "@type": "Organization",
        name: branding.siteName,
        url: absoluteSiteUrl("/"),
        ...(contact.phone ? { telephone: contact.phone } : {}),
        ...(contact.email ? { email: contact.email } : {}),
    };
    const schema = {
        "@context": "https://schema.org",
        "@type": "Service",
        name,
        description,
        url,
        provider,
        areaServed: {
            "@type": "Country",
            name: "Georgia",
        },
        serviceOutput: "Professional IT Infrastructure Solution",
        audience: {
            "@type": "BusinessAudience",
            audienceType: "Businesses",
        },
    };

    return (
        <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
        />
    );
}
