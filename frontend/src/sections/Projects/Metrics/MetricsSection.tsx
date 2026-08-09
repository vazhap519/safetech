import ProjectMetric from "@/components/Projects/ProjectMetric";
import { getBackendProjects } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function MetricsSection() {
    const [projects, { locale, translations }] = await Promise.all([
        getBackendProjects(),
        getSiteSettings(),
    ]);

    if (!projects.length) return null;

    const featuredCount = projects.filter((project) => project.featured).length;
    const categoryCount = new Set(
        projects.map((project) => project.category).filter(Boolean),
    ).size;
    const technologyCount = new Set(
        projects.map((project) => project.technology).filter(Boolean),
    ).size;

    const metrics = [
        {
            value: String(projects.length),
            label: translateText(
                translations,
                "projects.metrics.projects",
                locale,
                null,
            ),
        },
        {
            value: String(featuredCount),
            label: translateText(
                translations,
                "projects.metrics.featured",
                locale,
                null,
            ),
        },
        {
            value: String(categoryCount),
            label: translateText(
                translations,
                "projects.metrics.categories",
                locale,
                null,
            ),
        },
        {
            value: String(technologyCount),
            label: translateText(
                translations,
                "projects.metrics.technologies",
                locale,
                null,
            ),
        },
    ].filter((metric) => metric.label);

    if (!metrics.length) return null;

    const regionLabel = translateText(
        translations,
        "projects.metrics.region",
        locale,
        null,
    );

    return (
        <section
            aria-label={regionLabel || undefined}
            className="border-y border-outline-variant/10 bg-surface-container-lowest py-unit-xl"
        >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="grid grid-cols-2 gap-gutter md:grid-cols-4">
                    {metrics.map((metric) => (
                        <ProjectMetric key={metric.label} metric={metric} />
                    ))}
                </div>
            </div>
        </section>
    );
}
