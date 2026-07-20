import { absoluteSiteUrl } from "@/lib/seo";

type OrganizationContact = {
    phone?: string;
    email?: string;
    address?: string;
};

type OrganizationBranding = {
    logo?: string | null;
    footerLogo?: string | null;
    defaultImage: string;
};

export function buildOrganizationEntity({
    branding,
    contact,
    contactType,
    description,
    siteName,
    socialUrls = [],
    url,
}: {
    branding: OrganizationBranding;
    contact: OrganizationContact;
    contactType: "customer support" | "sales";
    description?: string;
    siteName: string;
    socialUrls?: string[];
    url: string;
}): Record<string, unknown> {
    const logo = absoluteSiteUrl(
        branding.logo || branding.footerLogo || branding.defaultImage,
    );
    const contactPoint =
        contact.phone || contact.email
            ? [
                  {
                      "@type": "ContactPoint",
                      contactType,
                      ...(contact.phone ? { telephone: contact.phone } : {}),
                      ...(contact.email ? { email: contact.email } : {}),
                      areaServed: "GE",
                      availableLanguage: ["ka", "en", "ru"],
                  },
              ]
            : undefined;

    return {
        "@type": "Organization",
        name: siteName,
        url,
        logo,
        image: logo,
        areaServed: "Georgia",
        ...(description ? { description } : {}),
        ...(contact.phone ? { telephone: contact.phone } : {}),
        ...(contact.email ? { email: contact.email } : {}),
        ...(contact.address
            ? {
                  address: {
                      "@type": "PostalAddress",
                      streetAddress: contact.address,
                      addressCountry: "GE",
                      addressLocality: "Tbilisi",
                  },
              }
            : {}),
        ...(contactPoint ? { contactPoint } : {}),
        ...(socialUrls.length ? { sameAs: socialUrls } : {}),
    };
}
