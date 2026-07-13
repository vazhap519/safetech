import ProjectMetric from "@/components/Projects/ProjectMetric";
import { getBackendProjects } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function MetricsSection() {
    const [projects, { locale, translations }] = await Promise.all([
        getBackendProjects(),
        getSiteSettings(),
    ]);
    const t = createTranslator(translations, locale);

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
            label: t("projects.metrics.projects", {
                ka: "პროექტი",
                en: "Projects",
                ru: "Проекты",
            }),
        },
        {
            value: String(featuredCount),
            label: t("projects.metrics.featured", {
                ka: "რჩეული",
                en: "Featured",
                ru: "Избранные",
            }),
        },
        {
            value: String(categoryCount),
            label: t("projects.metrics.categories", {
                ka: "კატეგორია",
                en: "Categories",
                ru: "Категории",
            }),
        },
        {
            value: String(technologyCount),
            label: t("projects.metrics.technologies", {
                ka: "ტექნოლოგია",
                en: "Technologies",
                ru: "Технологии",
            }),
        },
    ];

    return (
        <section
            aria-label={t("projects.metrics.region", {
                ka: "პროექტების სტატისტიკა",
                en: "Project statistics",
                ru: "Статистика проектов",
            })}
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
