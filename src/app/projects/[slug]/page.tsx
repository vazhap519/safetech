import type { Metadata } from "next";
import { notFound } from "next/navigation";

import ProjectDetailSchema from "@/components/seo/ProjectDetailSchema";
import { getBackendProject, getBackendProjectSlugs } from "@/lib/backend";
import { projectDetails } from "@/lib/projectDetails";
import { absoluteSiteUrl, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import ChallengesSection from "@/sections/Projects/Details/ChallengesSection";
import GallerySection from "@/sections/Projects/Details/GallerySection";
import ProcessSection from "@/sections/Projects/Details/ProcessSection";
import ProjectCtaSection from "@/sections/Projects/Details/ProjectCtaSection";
import ProjectHeroSection from "@/sections/Projects/Details/ProjectHeroSection";
import ProjectOverviewSection from "@/sections/Projects/Details/ProjectOverviewSection";
import RelatedProjectsSection from "@/sections/Projects/Details/RelatedProjectsSection";
import ResultsSection from "@/sections/Projects/Details/ResultsSection";
import SolutionsSection from "@/sections/Projects/Details/SolutionsSection";
import TestimonialSection from "@/sections/Projects/Details/TestimonialSection";

type ProjectPageProps = { params: Promise<{ slug: string }> };

export async function generateStaticParams() {
    const slugs = [
        ...new Set([
            ...projectDetails.map(({ slug }) => slug),
            ...(await getBackendProjectSlugs()),
        ]),
    ];

    return slugs.map((slug) => ({ slug }));
}

export async function generateMetadata({
    params,
}: ProjectPageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding }, project] = await Promise.all([
        getSiteSettings(),
        getBackendProject(slug),
    ]);
    const siteName = branding.siteName;

    if (!project) {
        return {
            title: withSiteTitle("პროექტი ვერ მოიძებნა", siteName),
            robots: { index: false, follow: false },
        };
    }

    const canonical = absoluteSiteUrl(`/projects/${project.slug}`);
    const image = absoluteSiteUrl(project.image || branding.defaultImage);
    const title = withSiteTitle(project.name, siteName);

    return {
        title,
        description: project.seoDescription,
        alternates: { canonical },
        openGraph: {
            title,
            description: project.seoDescription,
            type: "article",
            url: canonical,
            locale: "ka_GE",
            siteName,
            images: [{ url: image, alt: project.imageAlt || project.name }],
        },
        twitter: {
            card: "summary_large_image",
            title,
            description: project.seoDescription,
            images: [image],
        },
        robots: { index: true, follow: true },
    };
}

export default async function ProjectDetailPage({ params }: ProjectPageProps) {
    const { slug } = await params;
    const project = await getBackendProject(slug);

    if (!project) notFound();

    const relatedProjects = (
        await Promise.all(
            project.related.map(async (relatedProject) => {
                if (
                    !relatedProject.slug ||
                    relatedProject.slug === project.slug
                ) {
                    return null;
                }

                const resolvedProject = await getBackendProject(
                    relatedProject.slug,
                );

                if (!resolvedProject) {
                    return null;
                }

                return {
                    slug: resolvedProject.slug,
                    title: relatedProject.title || resolvedProject.name,
                    category:
                        relatedProject.category ||
                        resolvedProject.meta?.[0]?.value ||
                        "პროექტი",
                    image: relatedProject.image || resolvedProject.image,
                    imageAlt:
                        relatedProject.imageAlt ||
                        resolvedProject.imageAlt ||
                        resolvedProject.name,
                };
            }),
        )
    ).filter((item): item is NonNullable<typeof item> => Boolean(item));

    return (
        <article className="pt-20">
            <ProjectDetailSchema project={project} />
            <ProjectHeroSection project={project} />
            <ProjectOverviewSection project={project} />
            <ChallengesSection project={project} />
            <SolutionsSection project={project} />
            <ProcessSection project={project} />
            <GallerySection project={project} />
            <ResultsSection project={project} />
            {project.testimonial.quote ? (
                <TestimonialSection testimonial={project.testimonial} />
            ) : null}
            {relatedProjects.length ? (
                <RelatedProjectsSection projects={relatedProjects} />
            ) : null}
            <ProjectCtaSection />
        </article>
    );
}
