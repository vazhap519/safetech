import { getLanguageTag } from "@/lib/locales";
import { getBusinessProfileSchemaDetails } from "@/lib/business-profile-schema";
import {
    absoluteLocalizedUrl,
    absoluteSiteUrl,
    SITE_NAME,
} from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HomeSchema() {
    const [{ branding, locale, translations }, businessProfile] = await Promise.all([
        getSiteSettings(),
        getBusinessProfileSchemaDetails(),
    ]);
    const siteName = branding.siteName || SITE_NAME;
    const homeUrl = absoluteLocalizedUrl("/", locale);
    const organizationId = `${absoluteSiteUrl("/")}#organization`;
    const description = translateText(
        translations,
        "meta.home.description",
        locale,
        null,
    );
    const hasAddress = Boolean(
        businessProfile.city || businessProfile.postalCode || businessProfile.country,
    );
    const organizationNode = {
        "@type": "Organization",
        "@id": organizationId,
        ...(businessProfile.description
            ? { description: businessProfile.description }
            : {}),
        ...(hasAddress
            ? {
                  address: {
                      "@type": "PostalAddress",
                      ...(businessProfile.city
                          ? { addressLocality: businessProfile.city }
                          : {}),
                      ...(businessProfile.postalCode
                          ? { postalCode: businessProfile.postalCode }
                          : {}),
                      addressCountry: businessProfile.country,
                  },
              }
            : {}),
    };
    const websiteNode = {
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
    const schema = {
        "@context": "https://schema.org",
        "@graph": [organizationNode, websiteNode],
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
