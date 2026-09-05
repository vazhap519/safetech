import type { Metadata } from "next";
import { notFound } from "next/navigation";

import LocalServiceLinks from "@/components/seo/LocalServiceLinks";
import ServiceDetailView from "@/features/service-detail/ServiceDetailView";
import ServiceStructuredData from "@/features/service-detail/components/ServiceStructuredData";
import { getBackendService } from "@/lib/backend";
import { confirmBackendResourceNotFound } from "@/lib/backend-resource-status";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ServicePageProps = {
    params: Promise<{ slug: string }>;
};

function normalizedSeoTitle(value: string | null | undefined, siteName: string) {
    return (value || "")
        .replace(new RegExp(`\\s*[|—-]\\s*${siteName}\\s*$`, "i"), "")
        .replace(/\s+/g, " ")
        .trim()
        .toLocaleLowerCase();
}

function countryServiceTitle(serviceName: string, locale: string) {
    switch (locale) {
        case "en":
            return `${serviceName} in Georgia`;
        case "ru":
            return `${serviceName} в Грузии`;
        default:
            return `${serviceName} საქართველოში`;
    }
}

export async function generateMetadata({
    params,
}: ServicePageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding, locale, translations }, service, localLandings] =
        await Promise.all([
            getSiteSettings(),
            getBackendService(slug),
            getLocalServiceLandings(slug),
        ]);
    const siteName = branding.siteName;

    if (!service) {
        await confirmBackendResourceNotFound(
            `/services/${encodeURIComponent(slug)}`,
            { locale },
        );

        return {
            title: withSiteTitle(
                translateText(
                    translations,
                    "meta.service.notFound",
                    locale,
                    null,
                ),
                siteName,
            ),
            robots: { index: false, follow: false },
        };
    }

    const configuredTitle = service.seo?.title || service.title || service.name;
    const normalizedConfiguredTitle = normalizedSeoTitle(configuredTitle, siteName);
    const duplicatesLocalLanding = localLandings.some((landing) =>
        [landing.seo?.title, landing.title].some(
            (candidate) =>
                normalizedSeoTitle(candidate, siteName) === normalizedConfiguredTitle,
        ),
    );
    const metadataTitle = duplicatesLocalLanding
        ? countryServiceTitle(service.name || service.title || configuredTitle, locale)
        : configuredTitle;

    return createMetadata({
        title: metadataTitle,
        description:
            service.seo?.description ||
            service.seoDescription ||
            service.description,
        path: `/services/${service.slug}`,
        locale,
        keywords: service.seo?.keywords?.length
            ? service.seo.keywords
            : service.keywords,
        image:
            service.seo?.image ||
            service.heroImage ||
            branding.defaultImage ||
            undefined,
        siteName,
        noindex: Boolean(service.seo?.noindex),
    });
}

export default async function ServicePage({ params }: ServicePageProps) {
    const { slug } = await params;
    const [{ locale, socialSharing }, service] = await Promise.all([
        getSiteSettings(),
        getBackendService(slug),
    ]);

    if (!service) {
        await confirmBackendResourceNotFound(
            `/services/${encodeURIComponent(slug)}`,
            { locale },
        );
        notFound();
    }

    return (
        <>
            <ServiceStructuredData service={service} />
            <ServiceDetailView
                locale={locale}
                service={service}
                sharing={socialSharing}
            />
            <LocalServiceLinks locale={locale} serviceSlug={service.slug} />
        </>
    );
}
