import ProjectsSchema from "@/components/seo/ProjectsSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
import ProjectsCtaSection from "@/sections/Projects/Cta/ProjectsCtaSection";
import FeaturedProjectsSection from "@/sections/Projects/Featured/FeaturedProjectsSection";
import ProjectsGallerySection from "@/sections/Projects/Gallery/ProjectsGallerySection";
import ProjectsHeroSection from "@/sections/Projects/Hero/Hero";
import MetricsSection from "@/sections/Projects/Metrics/MetricsSection";
import StandardsSection from "@/sections/Projects/Standards/StandardsSection";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return createMetadata({
        title: t("meta.projects.title", {
            ka: "განხორციელებული IT და უსაფრთხოების პროექტები | SafeTech",
            en: "Completed IT and Security Projects | SafeTech",
            ru: "Реализованные IT- и охранные проекты | SafeTech",
        }),
        description: t("meta.projects.description", {
            ka: "დაათვალიერეთ SafeTech-ის დასრულებული CCTV, ქსელური, სერვერული და უსაფრთხოების ინფრასტრუქტურის პროექტები.",
            en: "Review completed SafeTech CCTV, networking, server, and security infrastructure projects.",
            ru: "Посмотрите реализованные проекты SafeTech по видеонаблюдению, сетевой, серверной и охранной инфраструктуре.",
        }),
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
