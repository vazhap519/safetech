import { getLanguageTag } from "@/lib/locales";
import { getLocalizedProjectPage } from "@/lib/project-api";
import { absoluteLocalizedUrl, SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ProjectSchemaItem = {
    name: string;
    url?: string;
    image?: string;
};

export default async function ProjectsSchema({ page = 1 }: { page?: number }) {
    const [{ branding, locale, translations }, projectPage] = await Promise.all([
        getSiteSettings(),
        getLocalizedProjectPage(undefined, page),
    ]);
    const siteName = branding.siteName || SITE_NAME;
    const name =
        translateText(translations, "meta.projects.title", locale, null) ||
        siteName;
    const description = translateText(
        translations,
        "meta.projects.description",
        locale,
        null,
    );

    const seen = new Set<string>();
    const items: ProjectSchemaItem[] = [];

    for (const project of projectPage.projects) {
        if (seen.has(project.title)) continue;
        seen.add(project.title);
        items.push({
            name: project.title,
            url: project.slug
                ? absoluteLocalizedUrl(`/projects/${project.slug}`, locale)
                : undefined,
        });
    }

    const schema = {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        name,
        ...(description ? { description } : {}),
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
