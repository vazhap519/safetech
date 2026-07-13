import {
    getBackendFeaturedProjects,
    getBackendProjectCards,
} from "@/lib/backend";
import { getLanguageTag } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

type ProjectSchemaItem = {
    name: string;
    url?: string;
    image?: string;
};

export default async function ProjectsSchema() {
    const [{ locale, translations }, featuredProjects, projects] =
        await Promise.all([
            getSiteSettings(),
            getBackendFeaturedProjects(),
            getBackendProjectCards(),
        ]);
    const t = createTranslator(translations, locale);

    const seen = new Set<string>();
    const items: ProjectSchemaItem[] = [];

    for (const project of projects) {
        if (seen.has(project.title)) continue;
        seen.add(project.title);
        items.push({
            name: project.title,
            url: project.slug
                ? absoluteLocalizedUrl(`/projects/${project.slug}`, locale)
                : undefined,
        });
    }

    for (const project of featuredProjects) {
        if (seen.has(project.title)) continue;
        seen.add(project.title);
        items.push({
            name: project.title,
            image: absoluteSiteUrl(project.image),
        });
    }

    const schema = {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        name: t("meta.projects.title", {
            ka: "განხორციელებული IT და უსაფრთხოების პროექტები | SafeTech",
            en: "Completed IT and Security Projects | SafeTech",
            ru: "Реализованные IT- и охранные проекты | SafeTech",
        }),
        description: t("meta.projects.description", {
            ka: "დაათვალიერეთ SafeTech-ის დასრულებული CCTV, ქსელური, სერვერული და უსაფრთხოების ინფრასტრუქტურის პროექტები.",
            en: "Review completed SafeTech CCTV, networking, server, and security infrastructure projects.",
            ru: "Посмотрите реализованные проекты SafeTech по видеонаблюдению, сетевой, серверной и охранной инфраструктуре.",
        }),
        url: absoluteLocalizedUrl("/projects", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity: {
            "@type": "ItemList",
            itemListElement: items.map((project, index) => ({
                "@type": "ListItem",
                position: index + 1,
                name: project.name,
                item: {
                    "@type": "CreativeWork",
                    name: project.name,
                    ...(project.url ? { url: project.url } : {}),
                    ...(project.image ? { image: project.image } : {}),
                },
            })),
        },
    };

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
