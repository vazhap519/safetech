import ProjectsPageContent from "@/components/pages/ProjectsPageContent";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.projects);
}

type ProjectsRouteProps = {
    searchParams?: Promise<{ category?: string }>;
};

export default function ProjectsPage({ searchParams }: ProjectsRouteProps) {
    return <ProjectsPageContent searchParams={searchParams} />;
}
