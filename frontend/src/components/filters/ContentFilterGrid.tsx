"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import ProjectCard from "@/components/Projects/ProjectCard";
import ServiceCardComponent from "@/components/Service/ServiceCard/ServiceCardComponent";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { Project } from "@/lib/projects";

type FilterKind = "projects" | "services";

type FilterCategory = {
    name: string;
    slug: string;
};

type ServiceItem = {
    category?: string;
    description: string;
    icon: string;
    slug: string;
    title: string;
};

type Props =
    | {
          activeCategory?: string;
          categories: FilterCategory[];
          items: Project[];
          kind: "projects";
      }
    | {
          activeCategory?: string;
          categories: FilterCategory[];
          items: ServiceItem[];
          kind: "services";
      };

function categoryHref(kind: FilterKind, slug: string) {
    const basePath = kind === "projects" ? "/projects" : "/services";

    return slug === "all"
        ? basePath
        : `${basePath}?category=${encodeURIComponent(slug)}`;
}

export default function ContentFilterGrid({
    activeCategory = "all",
    categories,
    items,
    kind,
}: Props) {
    const { t } = useLocalization();
    const allLabel = t(`${kind}.filters.all`, null) || t("filters.all", null);
    const filterAriaLabel = t(`${kind}.filters.aria`, null) || undefined;
    const normalizedActiveCategory = activeCategory || "all";
    const filterItems = [
        allLabel ? { name: allLabel, slug: "all" } : null,
        ...categories,
    ].filter((category): category is FilterCategory => Boolean(category?.name));
    const gridClassName =
        kind === "projects"
            ? "grid grid-cols-1 gap-gutter md:grid-cols-2 lg:grid-cols-3"
            : "mb-unit-xl grid gap-gutter md:grid-cols-2 lg:grid-cols-3";

    return (
        <>
            {filterItems.length > 1 ? (
                <div
                    aria-label={filterAriaLabel}
                    className="mb-unit-xl flex flex-wrap justify-center gap-unit-sm sm:gap-unit-md"
                    role="group"
                >
                    {filterItems.map((category) => {
                        const active = category.slug === normalizedActiveCategory;

                        return (
                            <LocalizedLink
                                aria-pressed={active}
                                className={`${
                                    active
                                        ? "bg-primary text-on-primary"
                                        : "glass-card text-on-surface hover:bg-surface-variant"
                                } rounded-full px-5 py-2.5 font-label-md text-label-md transition-colors sm:px-8`}
                                href={categoryHref(kind, category.slug)}
                                key={category.slug}
                                role="button"
                            >
                                {category.name}
                            </LocalizedLink>
                        );
                    })}
                </div>
            ) : null}
            <div aria-live="polite" className={gridClassName}>
                {kind === "projects"
                    ? (items as Project[]).map((project) => (
                          <ProjectCard
                              key={project.title || project.slug}
                              project={project}
                          />
                      ))
                    : (items as ServiceItem[]).map((service) => (
                          <ServiceCardComponent
                              key={service.slug}
                              service={service}
                          />
                      ))}
            </div>
        </>
    );
}
