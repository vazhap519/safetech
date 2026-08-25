import type { Metadata } from "next";
import { notFound } from "next/navigation";

import LocalServiceLandingView from "@/components/pages/LocalServiceLandingView";
import { confirmBackendResourceNotFound } from "@/lib/backend-resource-status";
import { getLocalServiceLanding } from "@/lib/local-service-landings";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

type LocalServicePageProps = {
    params: Promise<{ slug: string; location: string }>;
};

export async function generateMetadata({
    params,
}: LocalServicePageProps): Promise<Metadata> {
    const { slug, location } = await params;
    const [{ branding, locale }, landing] = await Promise.all([
        getSiteSettings(),
        getLocalServiceLanding(slug, location),
    ]);

    if (!landing) {
        await confirmBackendResourceNotFound(
            `/local-service-landings/${encodeURIComponent(slug)}/${encodeURIComponent(location)}`,
            { locale },
        );

        return {
            title: withSiteTitle("Page not found", branding.siteName),
            robots: { index: false, follow: false },
        };
    }

    return createMetadata({
        title: landing.seo?.title || landing.title,
        description:
            landing.seo?.description || landing.excerpt || landing.content,
        path: `/services/${landing.service.slug}/${landing.locationSlug}`,
        locale,
        keywords: landing.seo?.keywords?.length
            ? landing.seo.keywords
            : landing.keywords,
        image:
            landing.seo?.image ||
            landing.service.heroImage ||
            branding.defaultImage ||
            undefined,
        siteName: branding.siteName,
        noindex: Boolean(landing.seo?.noindex),
    });
}

export default async function LocalServicePage({
    params,
}: LocalServicePageProps) {
    const { slug, location } = await params;
    const [{ locale }, landing] = await Promise.all([
        getSiteSettings(),
        getLocalServiceLanding(slug, location),
    ]);

    if (!landing) {
        await confirmBackendResourceNotFound(
            `/local-service-landings/${encodeURIComponent(slug)}/${encodeURIComponent(location)}`,
            { locale },
        );
        notFound();
    }

    return <LocalServiceLandingView landing={landing} locale={locale} />;
}
