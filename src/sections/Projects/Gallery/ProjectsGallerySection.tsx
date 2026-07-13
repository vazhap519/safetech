import ProjectsGallery from "@/components/Projects/ProjectsGallery";
import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import { getBackendProjectCards } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ProjectsGallerySection() {
    const [projects, { locale, translations }] = await Promise.all([
        getBackendProjectCards(),
        getSiteSettings(),
    ]);

    if (!projects.length) return null;

    return (
        <section
            className="bg-surface-container-low/30 py-unit-xl"
            aria-labelledby="projects-gallery-title"
        >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="projects-gallery-title">
                    <ProjectsSectionHeader
                        centered
                        title={translateText(
                            translations,
                            "projects.gallery.title",
                            locale,
                            {
                                ka: "პროექტების კატალოგი",
                                en: "Project Catalog",
                                ru: "Каталог проектов",
                            },
                        )}
                        description={translateText(
                            translations,
                            "projects.gallery.description",
                            locale,
                            {
                                ka: "დაათვალიერეთ განხორციელებული გადაწყვეტილებები ინდუსტრიის მიხედვით",
                                en: "Browse completed solutions by industry",
                                ru: "Просмотрите реализованные решения по отраслям",
                            },
                        )}
                    />
                </div>
                <ProjectsGallery projects={projects} />
            </div>
        </section>
    );
}
