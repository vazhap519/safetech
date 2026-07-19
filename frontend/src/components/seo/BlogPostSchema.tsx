import type { Locale } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";
import JsonLd from "@/components/seo/JsonLd";

export default function BlogPostSchema({
                                           title,
                                           description,
                                           image,
                                           publishedAt,
                                           updatedAt,
                                           slug,
                                           locale,
                                           author,
                                       }: {
    title: string;
    description: string;
    image: string;
    publishedAt?: string;
    updatedAt?: string;
    slug: string;
    locale: Locale;
    author?: string;
}) {
    const published = publishedAt || updatedAt;
    const schema = {
        "@context": "https://schema.org",

        "@type": "BlogPosting",

        headline: title,

        description,

        image: [absoluteSiteUrl(image)],

        ...(published ? { datePublished: published } : {}),

        ...(updatedAt || published ? { dateModified: updatedAt || published } : {}),

        mainEntityOfPage: {
            "@type": "WebPage",

            "@id": absoluteLocalizedUrl(`/blog/${slug}`, locale),
        },

        author: {
            "@type": "Organization",

            name: author || "SafeTech",
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

    return <JsonLd data={schema} />;
}
