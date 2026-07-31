import { notFound } from "next/navigation";

import ShopPageContent from "@/components/pages/ShopPageContent";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import {
    createCategoryMetadataGenerator,
    getCategoryPageData,
} from "@/lib/category-data";

export const generateMetadata = createCategoryMetadataGenerator("shop");

export default async function ShopCategoryPage({
    params,
    searchParams,
}: {
    params: Promise<{ slug: string }>;
    searchParams?: Promise<Record<string, string | string[] | undefined>>;
}) {
    const { slug } = await params;
    const paramsFromQuery = (await searchParams) ?? {};
    const { category, locale, path } = await getCategoryPageData("shop", slug);

    if (!category) {
        notFound();
    }

    return (
        <>
            <ShopPageContent
                searchParams={{ ...paramsFromQuery, category: slug }}
                showPageSchema={false}
            />
            <CategorySeoContent category={category} locale={locale} path={path} />
        </>
    );
}
