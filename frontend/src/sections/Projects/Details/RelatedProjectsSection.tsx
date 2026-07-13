import RelatedProjectCard from "@/components/Projects/Details/RelatedProjectCard";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function RelatedProjectsSection({ projects }: { projects: ProjectDetail["related"] }) {
    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl" aria-labelledby="related-projects-title">
            <h2 className="mb-12 font-headline-md text-headline-md" id="related-projects-title">მსგავსი პროექტები</h2>
            <div className="grid gap-gutter md:grid-cols-3">{projects.map((project) => <RelatedProjectCard key={project.slug} project={project} />)}</div>
        </section>
    );
}
