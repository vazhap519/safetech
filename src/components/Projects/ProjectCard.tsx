import Link from "next/link";
import Icon from "@/components/ui/Icon";
import type { Project } from "@/lib/projects";

export default function ProjectCard({ project }: { project: Project }) {
    const color = project.accent === "secondary" ? "secondary" : "primary";

    return (
        <article className={`glass-card group rounded-xl border-l-4 p-6 ${color === "secondary" ? "border-secondary" : "border-primary"}`}>
            <div className="mb-4 flex items-start justify-between gap-4">
                <Icon
                    className={`text-4xl ${color === "secondary" ? "text-secondary" : "text-primary"}`}
                    name={project.icon}
                />
                <span className="rounded bg-surface-container-high px-2 py-1 text-[10px] font-bold uppercase text-on-surface-variant">{project.technology}</span>
            </div>
            <h3 className="mb-2 font-headline-md text-headline-md text-white">{project.title}</h3>
            <p className="mb-4 text-body-md leading-relaxed text-on-surface-variant">{project.description}</p>
            {project.slug ? (
                <Link className="inline-flex items-center gap-2 font-bold text-secondary" href={`/projects/${project.slug}`}>
                    დეტალები <span aria-hidden="true">→</span>
                </Link>
            ) : (
                <span className="inline-flex items-center gap-2 font-bold text-secondary/70">დასრულებული პროექტი</span>
            )}
        </article>
    );
}
