export type ProjectMetric = {
    value: string;
    label: string;
};

export type FeaturedProject = {
    title: string;
    category: string;
    image: string;
    imageAlt: string;
    videoUrl?: string | null;
    specs: Array<{ label: string; value: string }>;
};

export type Project = {
    slug?: string;
    title: string;
    description: string;
    category: string;
    icon: string;
    accent: "primary" | "secondary";
    technology: string;
    videoUrl?: string | null;
};
