export type StructuredDataNode = Record<string, unknown>;
export type StructuredDataValue = StructuredDataNode | StructuredDataNode[];

export type SeoConfiguration = {
    title?: string;
    description?: string;
    keywords?: string[];
    image?: string;
    noindex?: boolean;
    schema?: StructuredDataValue;
};

type BreadcrumbItem = {
    name: string;
    url: string;
};

export function buildBreadcrumbSchema(
    items: BreadcrumbItem[],
): StructuredDataNode {
    return {
        "@type": "BreadcrumbList",
        itemListElement: items.map((item, index) => ({
            "@type": "ListItem",
            position: index + 1,
            name: item.name,
            item: item.url,
        })),
    };
}
