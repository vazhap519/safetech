export type ServiceBenefit = {
    icon: string;
    title: string;
    description: string;
};

export type ServiceSolution = ServiceBenefit & {
    featured?: boolean;
};

export type ServiceStep = {
    title: string;
    description: string;
};

export type ServiceFaq = {
    question: string;
    answer: string;
};

export type RelatedService = {
    slug: string;
    icon: string;
    title: string;
    description: string;
};

export type ServiceDetail = {
    slug: string;
    name: string;
    eyebrow: string;
    title: string;
    description: string;
    seoDescription: string;
    keywords: string[];
    heroImage: string;
    highlights: string[];
    overview: {
        title: string;
        paragraphs: string[];
        stats: Array<{ value: string; label: string }>;
    };
    benefits: ServiceBenefit[];
    solutions: ServiceSolution[];
    industries: string[];
    process: ServiceStep[];
    faqs: ServiceFaq[];
    related: RelatedService[];
    seo?: {
        title?: string;
        description?: string;
        keywords?: string[];
        image?: string;
        noindex?: boolean;
        schema?: Record<string, unknown> | Array<Record<string, unknown>>;
    };
};
