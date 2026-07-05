import type { Metadata } from "next";
import { notFound } from "next/navigation";

import ServiceDetailView from "@/features/service-detail/ServiceDetailView";
import ServiceStructuredData from "@/features/service-detail/components/ServiceStructuredData";
import { serviceDetails } from "@/features/service-detail/data/services";
import { getBackendService, getBackendServiceSlugs } from "@/lib/backend";
import { absoluteSiteUrl, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

type ServicePageProps = {
    params: Promise<{ slug: string }>;
};

export async function generateStaticParams() {
    const slugs = [
        ...new Set([
            ...serviceDetails.map(({ slug }) => slug),
            ...(await getBackendServiceSlugs()),
        ]),
    ];

    return slugs.map((slug) => ({ slug }));
}

export async function generateMetadata({
    params,
}: ServicePageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding }, service] = await Promise.all([
        getSiteSettings(),
        getBackendService(slug),
    ]);
    const siteName = branding.siteName;

    if (!service) {
        return {
            title: withSiteTitle("სერვისი ვერ მოიძებნა", siteName),
            robots: { index: false, follow: false },
        };
    }

    const canonical = absoluteSiteUrl(`/services/${service.slug}`);
    const image = absoluteSiteUrl(service.heroImage || branding.defaultImage);
    const title = withSiteTitle(service.name, siteName);

    return {
        title,
        description: service.seoDescription,
        keywords: service.keywords,
        alternates: { canonical },
        openGraph: {
            type: "website",
            locale: "ka_GE",
            url: canonical,
            siteName,
            title,
            description: service.seoDescription,
            images: [{ url: image, alt: service.name }],
        },
        twitter: {
            card: "summary_large_image",
            title,
            description: service.seoDescription,
            images: [image],
        },
        robots: { index: true, follow: true },
    };
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
