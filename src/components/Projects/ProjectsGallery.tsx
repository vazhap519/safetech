"use client";

import { useMemo, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import type { Project, ProjectCategory } from "@/lib/projects";
import { projectCategories } from "@/lib/projects";
import ProjectCard from "./ProjectCard";

function getCategoryFallback(value: ProjectCategory) {
    switch (value) {
        case "all":
            return {
                ka: "ყველა",
                en: "All",
                ru: "Все",
            };
        case "offices":
            return {
                ka: "ოფისები",
                en: "Offices",
                ru: "Офисы",
            };
        case "hotels":
            return {
                ka: "სასტუმროები",
                en: "Hotels",
                ru: "Отели",
            };
        case "warehouses":
            return {
                ka: "საწყობები",
                en: "Warehouses",
                ru: "Склады",
            };
        case "factories":
            return {
                ka: "საწარმოები",
                en: "Factories",
                ru: "Производства",
            };
    }
}

export default function ProjectsGallery({ projects }: { projects: Project[] }) {
    const [activeCategory, setActiveCategory] = useState<ProjectCategory>("all");
    const { t } = useLocalization();
    const visibleProjects = useMemo(
        () =>
            activeCategory === "all"
                ? projects
                : projects.filter((project) => project.category === activeCategory),
        [activeCategory, projects],
    );

    return (
        <>
            <div
                aria-label={t("projects.filters.aria", {
                    ka: "პროექტების კატეგორიები",
                    en: "Project categories",
                    ru: "Категории проектов",
                })}
                className="mb-unit-xl flex flex-wrap justify-center gap-unit-sm sm:gap-unit-md"
                role="group"
            >
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
                            {t(
                                category.labelKey,
                                getCategoryFallback(category.value),
                            )}
                        </button>
                    );
                })}
            </div>
            <div
                aria-live="polite"
                className="grid grid-cols-1 gap-gutter md:grid-cols-2 lg:grid-cols-3"
            >
                {visibleProjects.map((project) => (
                    <ProjectCard key={project.title} project={project} />
                ))}
            </div>
        </>
    );
}
