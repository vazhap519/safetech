import ProjectsSchema from "@/components/seo/ProjectsSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { getCurrentLocale } from "@/lib/locale-server";
import { firstSearchParam, parsePageNumber } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import { redirect } from "next/navigation";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

type ProjectsPageContentProps = {
    searchParams?: Promise<{ category?: string; page?: string }> | { category?: string; page?: string };
    showPageSchema?: boolean;
    showHero?: boolean;
};

export default async function ProjectsPageContent({
    searchParams,
    showPageSchema = true,
    showHero = true,
}: ProjectsPageContentProps) {
    const resolvedSearchParams = await searchParams;
    const category = firstSearchParam(resolvedSearchParams?.category);
    const page = parsePageNumber(resolvedSearchParams?.page) ?? 1;

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
            {showHero ? <ProjectsHeroSection /> : null}
            <MetricsSection />
            <FeaturedProjectsSection />
            <ProjectsGallerySection category={category || undefined} page={page} />
            <StandardsSection />
            <ProjectsCtaSection />
        </>
    );
}
