import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ContactSchema() {
    const { contact, branding } = await getSiteSettings();
    const schema = {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        name: `${branding.siteName} Contact`,
        url: absoluteSiteUrl("/contact"),
        mainEntity: {
            "@type": "Organization",
            name: branding.siteName,
            url: absoluteSiteUrl("/"),
            telephone: contact.phone,
            email: contact.email,
            logo: branding.logo || branding.footerLogo || branding.defaultImage,
            address: {
                "@type": "PostalAddress",
                streetAddress: contact.address,
                addressCountry: "GE",
                addressLocality: "Tbilisi",
            },
            contactPoint: [
                {
                    "@type": "ContactPoint",
                    contactType: "sales",
                    telephone: contact.phone,
                    email: contact.email,
                    availableLanguage: ["ka", "en"],
                },
            ],
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
