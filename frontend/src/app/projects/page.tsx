import ProjectsPageContent from "@/components/pages/ProjectsPageContent";
import CorePageFallback from "@/components/seo/CorePageFallback";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { getCurrentLocale } from "@/lib/locale-server";
import { parsePageNumber, withPaginationMetadata } from "@/lib/pagination";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata({ searchParams }: ProjectsRouteProps) {
    const resolvedSearchParams = await searchParams;
    const page = parsePageNumber(resolvedSearchParams?.page) ?? 1;
    const [metadata, locale] = await Promise.all([
        createCmsPageMetadata(PAGE_SEO_PRESETS.projects),
        getCurrentLocale(),
    ]);

    return withPaginationMetadata(metadata, "/projects", page, locale);
}

type ProjectsRouteProps = {
    searchParams?: Promise<{ category?: string; page?: string }>;
};

export default function ProjectsPage({ searchParams }: ProjectsRouteProps) {
    return (
        <>
            <CorePageFallback pageKey="projects" />
            <ProjectsPageContent searchParams={searchParams} />
        </>
    );
}
