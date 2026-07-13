import { absoluteSiteUrl } from "@/lib/seo";

export default function BlogSchema() {
    const schema = {
        "@context": "https://schema.org",

        "@type": "Blog",

        name: "SafeTech Blog",

        url: absoluteSiteUrl("/blog"),

        publisher: {
            "@type": "Organization",
            name: "SafeTech",
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
