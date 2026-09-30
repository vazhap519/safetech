import ServicesPageContent from "@/components/pages/ServicesPageContent";
import CorePageFallback from "@/components/seo/CorePageFallback";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata({
    searchParams,
}: ServicesRouteProps) {
    const metadata = await createCmsPageMetadata(PAGE_SEO_PRESETS.services);
    const resolvedSearchParams = await searchParams;
    const hasFilteredState = Boolean(
        resolvedSearchParams?.category || resolvedSearchParams?.service,
    );

    if (!hasFilteredState) return metadata;

    return {
        ...metadata,
        robots: {
            index: false,
            follow: true,
            googleBot: {
                index: false,
                follow: true,
                "max-image-preview": "large" as const,
                "max-snippet": -1,
                "max-video-preview": -1,
            },
        },
    };
}

type ServicesRouteProps = {
    searchParams?: Promise<{ category?: string; service?: string }>;
};

export default function ServicesPage({ searchParams }: ServicesRouteProps) {
    return (
        <>
            <CorePageFallback pageKey="services" />
            <ServicesPageContent searchParams={searchParams} />
        </>
    );
}
