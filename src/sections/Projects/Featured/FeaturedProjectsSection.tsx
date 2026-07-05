import FeaturedProjectCard from "@/components/Projects/FeaturedProjectCard";
import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import { getBackendFeaturedProjects } from "@/lib/backend";

export default async function FeaturedProjectsSection() {
    const featuredProjects = await getBackendFeaturedProjects();
    return (
        <section className="bg-background py-unit-xl" aria-labelledby="featured-projects-title">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="featured-projects-title"><ProjectsSectionHeader title="რჩეული პროექტები" description="ჩვენი მასშტაბური ინფრასტრუქტურული ინსტალაციები" /></div>
                <div className="grid grid-cols-1 gap-gutter md:grid-cols-2 xl:grid-cols-3">
                    {featuredProjects.map((project) => <FeaturedProjectCard key={project.title} project={project} />)}
                </div>
            </div>
        </section>
    );
}
