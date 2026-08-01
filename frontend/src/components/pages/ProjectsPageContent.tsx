import ProjectsSchema from "@/components/seo/ProjectsSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { getCurrentLocale } from "@/lib/locale-server";
import { firstSearchParam } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import { redirect } from "next/navigation";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

type ProjectsPageContentProps = {
    searchParams?: Promise<{ category?: string }> | { category?: string };
    showPageSchema?: boolean;
};

export default async function ProjectsPageContent({
    searchParams,
    showPageSchema = true,
}: ProjectsPageContentProps) {
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
