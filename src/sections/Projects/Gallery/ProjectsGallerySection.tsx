import ProjectsGallery from "@/components/Projects/ProjectsGallery";
import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import { getBackendProjectCards } from "@/lib/backend";

export default async function ProjectsGallerySection() {
    const projects = await getBackendProjectCards();
    return (
        <section className="bg-surface-container-low/30 py-unit-xl" aria-labelledby="projects-gallery-title">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="projects-gallery-title"><ProjectsSectionHeader centered title="პროექტების კატალოგი" description="დაათვალიერეთ განხორციელებული გადაწყვეტილებები ინდუსტრიის მიხედვით" /></div>
                <ProjectsGallery projects={projects} />
            </div>
        </section>
    );
}
