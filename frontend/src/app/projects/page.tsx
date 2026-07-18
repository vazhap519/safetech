import ProjectsSchema from "@/components/seo/ProjectsSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();

    return createMetadata({
        title: translateText(translations, "meta.projects.title", locale, null),
        description: translateText(
            translations,
            "meta.projects.description",
            locale,
            null,
        ),
        path: "/projects",
        locale,
        keywords: [
            "IT projects",
            "CCTV projects",
            "network infrastructure",
            "server infrastructure",
            "SafeTech",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

type ProjectsPageProps = {
    searchParams?: Promise<{
        category?: string;
    }>;
};

export default async function ProjectsPage({ searchParams }: ProjectsPageProps) {
    const category = (await searchParams)?.category;

    return (
        <>
            <ProjectsSchema />
            <ProjectsHeroSection />
            <MetricsSection />
            <FeaturedProjectsSection />
            <ProjectsGallerySection category={category} />
            <StandardsSection />
            <ProjectsCtaSection />
        </>
    );
}
