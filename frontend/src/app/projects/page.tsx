import ProjectsPageContent from "@/components/pages/ProjectsPageContent";
import CorePageFallback from "@/components/seo/CorePageFallback";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.projects);
}

type ProjectsRouteProps = {
    searchParams?: Promise<{ category?: string }>;
};

export default function ProjectsPage({ searchParams }: ProjectsRouteProps) {
    return (
        <>
            <CorePageFallback pageKey="projects" />
            <ProjectsPageContent searchParams={searchParams} />
        </>
    );
}
