import JsonLd from "@/components/seo/JsonLd";
import type { BackendProduct } from "@/lib/backend";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl, DEFAULT_SOCIAL_IMAGE } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { buildBreadcrumbSchema } from "@/lib/structured-data";
import { createTranslator } from "@/lib/translations";

export default async function ProductDetailSchema({
    product,
}: {
    product: BackendProduct;
}) {
    const { branding, contact, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const url = absoluteLocalizedUrl(`/shop/${product.slug}`, locale);
    const productImage =
        product.image || branding.defaultImage || DEFAULT_SOCIAL_IMAGE;
    const organizationLogo =
        branding.logo ||
        branding.footerLogo ||
        branding.defaultImage ||
        DEFAULT_SOCIAL_IMAGE;
    const graph: Array<Record<string, unknown>> = [
        {
            "@type": "Product",
            "@id": `${url}#product`,
            name: product.seo?.title || product.name,
            description: product.seo?.description || product.shortDescription,
            image: absoluteSiteUrl(productImage),
            url,
            brand: {
                "@type": "Brand",
                name: branding.siteName,
            },
            manufacturer: {
                "@type": "Organization",
                name: branding.siteName,
                url: absoluteLocalizedUrl("/", locale),
                logo: absoluteSiteUrl(organizationLogo),
            },
            ...(product.category?.name
                ? { category: product.category.name }
                : {}),
            ...(product.price !== null && product.price !== undefined
                ? {
                      offers: {
                          "@type": "Offer",
                          price: product.price,
                          priceCurrency: product.currency || "GEL",
                          availability: "https://schema.org/InStock",
                          url,
                      },
                  }
                : contact.phone || contact.email
                  ? {
                        offers: {
                            "@type": "Offer",
                            availability: "https://schema.org/InStock",
                            priceSpecification: {
                                "@type": "PriceSpecification",
                                priceCurrency: product.currency || "GEL",
                            },
                            seller: {
                                "@type": "Organization",
                                name: branding.siteName,
                                ...(contact.phone ? { telephone: contact.phone } : {}),
                                ...(contact.email ? { email: contact.email } : {}),
                            },
                            url,
                        },
                    }
                  : {}),
            inLanguage: getLanguageTag(locale),
        },
        buildBreadcrumbSchema([
            {
                name: t("nav.home", {
                    ka: "მთავარი",
                    en: "Home",
                    ru: "Главная",
                }),
                url: absoluteLocalizedUrl("/", locale),
            },
            {
                name: t("nav.shop", {
                    ka: "მაღაზია",
                    en: "Shop",
                    ru: "Магазин",
                }),
                url: absoluteLocalizedUrl("/shop", locale),
            },
            {
                name: product.name,
                url,
            },
        ]),
    ];

    if (product.seo?.schema) {
        return <JsonLd data={product.seo.schema} />;
    }

    return (
        <JsonLd
            data={{
                "@context": "https://schema.org",
                "@graph": graph,
            }}
        />
    );
}
