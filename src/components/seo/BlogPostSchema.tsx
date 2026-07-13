import { absoluteSiteUrl } from "@/lib/seo";

export default function BlogPostSchema({
                                           title,
                                           description,
                                           image,
                                           publishedAt,
                                           updatedAt,
                                           slug,
                                       }: {
    title: string;
    description: string;
    image: string;
    publishedAt: string;
    updatedAt?: string;
    slug: string;
}) {
    const schema = {
        "@context": "https://schema.org",

        "@type": "BlogPosting",

        headline: title,

        description,

        image: [image],

        datePublished: publishedAt,

        dateModified: updatedAt || publishedAt,

        mainEntityOfPage: {
            "@type": "WebPage",

            "@id": absoluteSiteUrl(`/blog/${slug}`),
        },

        author: {
            "@type": "Organization",

            name: "SafeTech",
        },

        publisher: {
            "@type": "Organization",

            name: "SafeTech",

            logo: {
                "@type": "ImageObject",
                url: absoluteSiteUrl("/icon-192.png"),
            },
        },
    };

    return (
        <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema),
            }}
        />
    );
}
