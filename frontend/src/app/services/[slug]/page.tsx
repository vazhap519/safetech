import type { Metadata } from "next";
import { notFound } from "next/navigation";

import ServiceDetailView from "@/features/service-detail/ServiceDetailView";
import ServiceStructuredData from "@/features/service-detail/components/ServiceStructuredData";
import { getBackendService } from "@/lib/backend";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ServicePageProps = {
    params: Promise<{ slug: string }>;
};

export async function generateMetadata({
    params,
}: ServicePageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding, locale, translations }, service] = await Promise.all([
        getSiteSettings(),
        getBackendService(slug),
    ]);
    const siteName = branding.siteName;

    if (!service) {
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

    return createMetadata({
        title: service.seo?.title || service.title || service.name,
        description:
            service.seo?.description ||
            service.seoDescription ||
            service.description,
        path: `/services/${service.slug}`,
        locale,
        keywords: service.seo?.keywords?.length
            ? service.seo.keywords
            : service.keywords,
        image: service.seo?.image || service.heroImage || branding.defaultImage,
        siteName,
        noindex: Boolean(service.seo?.noindex),
    });
}

export default async function ServicePage({ params }: ServicePageProps) {
    const { slug } = await params;
    const service = await getBackendService(slug);

    if (!service) notFound();

    return (
        <>
            <ServiceStructuredData service={service} />
            <ServiceDetailView service={service} />
        </>
    );
}
