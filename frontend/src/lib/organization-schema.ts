import { absoluteSiteUrl, DEFAULT_SOCIAL_IMAGE } from "@/lib/seo";

type OrganizationContact = {
    phone?: string;
    phones?: string[];
    email?: string;
    address?: string;
};

type OrganizationBranding = {
    logo?: string | null;
    footerLogo?: string | null;
    defaultImage?: string | null;
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
        branding.logo ||
            branding.footerLogo ||
            branding.defaultImage ||
            DEFAULT_SOCIAL_IMAGE,
    );
    const phones = [
        ...(contact.phones ?? []),
        ...(contact.phone ? [contact.phone] : []),
    ].filter(Boolean);
    const uniquePhones = [...new Set(phones)];
    const primaryPhone = uniquePhones[0];
    const contactPoint =
        uniquePhones.length || contact.email
            ? [
                  ...(uniquePhones.length
                      ? uniquePhones.map((phone) => ({
                            "@type": "ContactPoint",
                            contactType,
                            telephone: phone,
                            ...(contact.email ? { email: contact.email } : {}),
                            areaServed: "GE",
                            availableLanguage: ["ka", "en", "ru"],
                        }))
                      : [
                            {
                                "@type": "ContactPoint",
                                contactType,
                                ...(contact.email ? { email: contact.email } : {}),
                                areaServed: "GE",
                                availableLanguage: ["ka", "en", "ru"],
                            },
                        ]),
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
        ...(primaryPhone ? { telephone: primaryPhone } : {}),
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
