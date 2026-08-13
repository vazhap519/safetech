export type ProjectDetailCard = {
    icon: string;
    title: string;
    description: string;
    featured?: boolean;
};

export type ProjectDetail = {
    slug: string;
    name: string;
    title: string;
    description: string;
    seoDescription: string;
    image: string;
    imageAlt: string;
    videoUrl?: string | null;
    publishedAt?: string | null;
    updated_at?: string | null;
    meta: Array<{ label: string; value: string }>;
    scope: Array<{ value: string; label: string }>;
    specs: Array<{ value: string; label: string }>;
    challenges: ProjectDetailCard[];
    solutions: ProjectDetailCard[];
    process: Array<{ title: string; description: string }>;
    gallery: Array<{ src: string; alt: string }>;
    results: Array<{
        value: string;
        title: string;
        description: string;
        accent: "primary" | "secondary";
    }>;
    related: Array<{
        translationIndex?: number;
        slug: string;
        title: string;
        category: string;
        image: string;
        imageAlt: string;
    }>;
    seo?: SeoConfiguration;
};
import type { SeoConfiguration } from "@/lib/structured-data";
