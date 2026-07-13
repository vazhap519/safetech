import ProjectDetailCard from "@/components/Projects/Details/ProjectDetailCard";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function SolutionsSection({ project }: { project: ProjectDetail }) {
    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl" aria-labelledby="project-solutions-title">
            <h2 className="mb-12 font-headline-xl text-headline-xl" id="project-solutions-title">გადაწყვეტა</h2>
            <div className="grid gap-gutter md:grid-cols-2 lg:grid-cols-4">{project.solutions.map((card) => <ProjectDetailCard card={card} key={card.title} />)}</div>
        </section>
    );
}
