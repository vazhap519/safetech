import ProjectsSchema from "@/components/seo/ProjectsSchema";
import { redirect } from "next/navigation";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import { getCurrentLocale } from "@/lib/locale-server";
import { firstSearchParam } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.projects);
}

type ProjectsPageProps = {
    searchParams?: Promise<{
        category?: string;
    }> | {
        category?: string;
    };
    showPageSchema?: boolean;
};

export default async function ProjectsPage({
    searchParams,
    showPageSchema = true,
}: ProjectsPageProps) {
    const category = firstSearchParam((await searchParams)?.category);

    if (showPageSchema && category) {
        const locale = await getCurrentLocale();
        const path = category === "all"
            ? "/projects"
            : `/projects/category/${encodeURIComponent(category)}`;

        redirect(localizeHref(path, locale));
    }

    return (
        <>
            {showPageSchema ? (
                <CmsPageSchema pageKey="projects" fallback={<ProjectsSchema />} />
            ) : null}
            <ProjectsHeroSection />
            <MetricsSection />
            <FeaturedProjectsSection />
            <ProjectsGallerySection category={category || undefined} />
            <StandardsSection />
            <ProjectsCtaSection />
        </>
    );
}
