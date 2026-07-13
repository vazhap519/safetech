export const projectCategories = [
    { value: "all", labelKey: "projects.filters.all" },
    { value: "offices", labelKey: "projects.filters.offices" },
    { value: "hotels", labelKey: "projects.filters.hotels" },
    { value: "warehouses", labelKey: "projects.filters.warehouses" },
    { value: "factories", labelKey: "projects.filters.factories" },
] as const;

export type ProjectCategory = (typeof projectCategories)[number]["value"];

export type ProjectMetric = {
    value: string;
    label: string;
};

export type FeaturedProject = {
    title: string;
    category: string;
    image: string;
    imageAlt: string;
    specs: Array<{ label: string; value: string }>;
};

export type Project = {
    slug?: string;
    title: string;
    description: string;
    category: Exclude<ProjectCategory, "all">;
    icon: string;
    accent: "primary" | "secondary";
    technology: string;
};

export type ProjectStandard = {
    title: string;
    description: string;
    icon: string;
    accent: "primary" | "secondary";
};
