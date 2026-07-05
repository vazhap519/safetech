import {
    getBackendFeaturedProjects,
    getBackendProjectCards,
} from "@/lib/backend";
import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

type ProjectSchemaItem = {
    name: string;
    url?: string;
    image?: string;
};

export default async function ProjectsSchema() {
    const [{ branding }, featuredProjects, projects] = await Promise.all([
        getSiteSettings(),
        getBackendFeaturedProjects(),
        getBackendProjectCards(),
    ]);

    const seen = new Set<string>();
    const items: ProjectSchemaItem[] = [];

    for (const project of projects) {
        if (seen.has(project.title)) continue;
        seen.add(project.title);
        items.push({
            name: project.title,
            url: project.slug
                ? absoluteSiteUrl(`/projects/${project.slug}`)
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
        name: `${branding.siteName}-ის ინფრასტრუქტურული პროექტები`,
        description:
            `${branding.siteName}-ის უსაფრთხოების, ქსელური და სერვერული ინფრასტრუქტურის განხორციელებული პროექტები.`,
        url: absoluteSiteUrl("/projects"),
        inLanguage: "ka-GE",
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
