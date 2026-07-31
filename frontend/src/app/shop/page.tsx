import ShopPageContent from "@/components/pages/ShopPageContent";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.shop);
}

type ShopRouteProps = {
    searchParams?: Promise<Record<string, string | string[] | undefined>>;
};

export default function ShopPage({ searchParams }: ShopRouteProps) {
    return <ShopPageContent searchParams={searchParams} />;
}
