import FeaturedProjectCard from "@/components/Projects/FeaturedProjectCard";
import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import { getBackendFeaturedProjects } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FeaturedProjectsSection() {
    const [featuredProjects, { locale, translations }] = await Promise.all([
        getBackendFeaturedProjects(),
        getSiteSettings(),
    ]);

    if (!featuredProjects.length) return null;

    return (
        <section
            className="bg-background py-unit-xl"
            aria-labelledby="featured-projects-title"
        >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="featured-projects-title">
                    <ProjectsSectionHeader
                        title={translateText(
                            translations,
                            "projects.featured.title",
                            locale,
                            null,
                        )}
                        description={translateText(
                            translations,
                            "projects.featured.description",
                            locale,
                            null,
                        )}
                    />
                </div>
                <div className="grid grid-cols-1 gap-gutter md:grid-cols-2 xl:grid-cols-3">
                    {featuredProjects.map((project) => (
                        <FeaturedProjectCard key={project.slug} project={project} />
                    ))}
                </div>
            </div>
        </section>
    );
}
