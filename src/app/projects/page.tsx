import ProjectsSchema from "@/components/seo/ProjectsSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

export async function generateMetadata() {
    const { branding } = await getSiteSettings();

    return createMetadata({
        title: "ინფრასტრუქტურული პროექტები",
        description:
            "SafeTech-ის განხორციელებული CCTV, ქსელური, დაშვების კონტროლისა და სერვერული ინფრასტრუქტურის პროექტები საქართველოში.",
        path: "/projects",
        keywords: [
            "IT პროექტები",
            "CCTV პროექტები",
            "ქსელური ინფრასტრუქტურა",
            "სერვერული ინფრასტრუქტურა",
            "SafeTech",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function ProjectsPage() {
    return (
        <>
            <ProjectsSchema />
            <ProjectsHeroSection />
            <MetricsSection />
            <FeaturedProjectsSection />
            <ProjectsGallerySection />
            <StandardsSection />
            <ProjectsCtaSection />
        </>
    );
}
