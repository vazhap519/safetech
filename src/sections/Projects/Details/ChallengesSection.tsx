import ProjectDetailCard from "@/components/Projects/Details/ProjectDetailCard";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function ChallengesSection({ project }: { project: ProjectDetail }) {
    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl" aria-labelledby="challenges-title">
            <h2 className="mb-12 text-center font-headline-xl text-headline-xl" id="challenges-title">გამოწვევა</h2>
            <div className="grid gap-gutter md:grid-cols-3">{project.challenges.map((card) => <ProjectDetailCard card={card} key={card.title} tone="danger" />)}</div>
        </section>
    );
}
