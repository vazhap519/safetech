import type { ProjectDetail } from "@/lib/projectDetails";
import { absoluteSiteUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ProjectDetailSchema({
    project,
}: {
    project: ProjectDetail;
}) {
    const { branding } = await getSiteSettings();
    const url = absoluteSiteUrl(`/projects/${project.slug}`);
    const schema = {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "CreativeWork",
                "@id": `${url}#project`,
                name: project.name,
                description: project.seoDescription,
                image: absoluteSiteUrl(project.image || branding.defaultImage),
                url,
                creator: {
                    "@type": "Organization",
                    name: branding.siteName,
                    url: absoluteSiteUrl("/"),
                    logo: absoluteSiteUrl(
                        branding.logo || branding.footerLogo || branding.defaultImage,
                    ),
                },
                inLanguage: "ka-GE",
            },
            {
                "@type": "BreadcrumbList",
                itemListElement: [
                    {
                        "@type": "ListItem",
                        position: 1,
                        name: "მთავარი",
                        item: absoluteSiteUrl("/"),
                    },
                    {
                        "@type": "ListItem",
                        position: 2,
                        name: "პროექტები",
                        item: absoluteSiteUrl("/projects"),
                    },
                    {
                        "@type": "ListItem",
                        position: 3,
                        name: project.name,
                        item: url,
                    },
                ],
            },
        ],
    };

    return (
        <script
            dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema).replace(/</g, "\\u003c"),
            }}
            type="application/ld+json"
        />
    );
}
