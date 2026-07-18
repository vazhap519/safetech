import RelatedProjectCard from "@/components/Projects/Details/RelatedProjectCard";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function RelatedProjectsSection({
    projects,
}: {
    projects: ProjectDetail["related"];
}) {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "project.detail.related.title",
        locale,
        null,
    );
    const relatedProjects = projects.filter(
        (project) => project.title || project.category,
    );

    if (!relatedProjects.length) return null;

    return (
        <section
            aria-labelledby={title ? "related-projects-title" : undefined}
            className="mx-auto max-w-container-max px-margin-desktop py-unit-xl"
        >
            {title ? (
                <h2
                    className="mb-12 font-headline-md text-headline-md"
                    id="related-projects-title"
                >
                    {title}
                </h2>
            ) : null}
            <div className="grid gap-gutter md:grid-cols-3">
                {relatedProjects.map((project) => (
                    <RelatedProjectCard key={project.slug} project={project} />
                ))}
            </div>
        </section>
    );
}
