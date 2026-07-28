import "server-only";

import {
    getCategories,
    getProductCategories,
    getProjectCategories,
    getServiceCategories,
} from "@/lib/datafetch";
import { categoryMetadata, findCategory } from "@/lib/categorySeo";
import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata } from "@/lib/seo";

export type CategoryKind = "blog" | "projects" | "services" | "shop";

const categoryLoaders = {
    blog: getCategories,
    projects: getProjectCategories,
    services: getServiceCategories,
    shop: getProductCategories,
} as const;

const missingCategoryCopy = {
    blog: {
        title: "Category not found",
        description: "The requested blog category does not exist.",
    },
    projects: {
        title: "Category not found",
        description: "The requested project category does not exist.",
    },
    services: {
        title: "Category not found",
        description: "The requested service category does not exist.",
    },
    shop: {
        title: "Category not found",
        description: "The requested product category does not exist.",
    },
} as const;

function categoryPath(kind: CategoryKind, slug: string): string {
    if (kind === "shop") {
        return `/shop/category/${encodeURIComponent(slug)}`;
    }

    return `/${kind}/category/${encodeURIComponent(slug)}`;
}

export async function getCategoryPageData(kind: CategoryKind, slug: string) {
    const locale = await getCurrentLocale();
    const response = await categoryLoaders[kind]({ locale }).catch(() => null);

    return {
        category: findCategory(response, slug),
        locale,
        path: categoryPath(kind, slug),
    };
}

export function createCategoryMetadataGenerator(kind: CategoryKind) {
    return async function generateCategoryMetadata({
        params,
    }: {
        params: Promise<{ slug: string }>;
    }) {
        const { slug } = await params;
        const { category, locale, path } = await getCategoryPageData(kind, slug);

        if (!category) {
            return createMetadata({
                ...missingCategoryCopy[kind],
                path,
                locale,
                noindex: true,
            });
        }

        return categoryMetadata({ category, path, locale, kind });
    };
}
