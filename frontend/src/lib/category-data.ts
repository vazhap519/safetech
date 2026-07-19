import "server-only";

import {
    getCategories,
    getProjectCategories,
    getServiceCategories,
} from "@/lib/datafetch";
import { findCategory } from "@/lib/categorySeo";
import { getCurrentLocale } from "@/lib/locale-server";

export type CategoryKind = "blog" | "projects" | "services";

const categoryLoaders = {
    blog: getCategories,
    projects: getProjectCategories,
    services: getServiceCategories,
} as const;

export function categoryPath(kind: CategoryKind, slug: string): string {
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
