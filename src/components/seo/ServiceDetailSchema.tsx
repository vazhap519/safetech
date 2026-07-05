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
    const { contact } = await getSiteSettings();
    const schema = {
        "@context": "https://schema.org",
        "@type": "Service",
        name,
        description,
        url,
        provider: {
            "@type": "Organization",
            name: "SafeTech",
            url: absoluteSiteUrl("/"),
            telephone: contact.phone,
            email: contact.email,
        },
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
