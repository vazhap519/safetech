import ServicesPageContent from "@/components/pages/ServicesPageContent";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.services);
}

type ServicesRouteProps = {
    searchParams?: Promise<{ category?: string }>;
};

export default function ServicesPage({ searchParams }: ServicesRouteProps) {
    return <ServicesPageContent searchParams={searchParams} />;
}
