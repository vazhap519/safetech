"use client";

import { useMemo, useState } from "react";
import type { Project, ProjectCategory } from "@/lib/projects";
import { projectCategories } from "@/lib/projects";
import ProjectCard from "./ProjectCard";

export default function ProjectsGallery({ projects }: { projects: Project[] }) {
    const [activeCategory, setActiveCategory] = useState<ProjectCategory>("all");
    const visibleProjects = useMemo(
        () => activeCategory === "all" ? projects : projects.filter((project) => project.category === activeCategory),
        [activeCategory, projects],
    );

    return (
        <>
            <div aria-label="პროექტების კატეგორიები" className="mb-unit-xl flex flex-wrap justify-center gap-unit-sm sm:gap-unit-md" role="group">
                {projectCategories.map((category) => {
                    const active = category.value === activeCategory;
                    return (
                        <button
                            aria-pressed={active}
                            className={`${active ? "bg-primary text-on-primary" : "glass-card text-on-surface hover:bg-surface-variant"} rounded-full px-5 py-2.5 font-label-md text-label-md transition-colors sm:px-8`}
                            key={category.value}
                            onClick={() => setActiveCategory(category.value)}
                            type="button"
                        >
                            {category.label}
                        </button>
                    );
                })}
            </div>
            <div aria-live="polite" className="grid grid-cols-1 gap-gutter md:grid-cols-2 lg:grid-cols-3">
                {visibleProjects.map((project) => <ProjectCard key={project.title} project={project} />)}
            </div>
        </>
    );
}
