import type { Metadata } from "next";
import { notFound } from "next/navigation";

import ProjectDetailSchema from "@/components/seo/ProjectDetailSchema";
import ContentShareButtons from "@/components/social/ContentShareButtons";
import { confirmBackendResourceNotFound } from "@/lib/backend-resource-status";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import { getLocalizedProject } from "@/lib/project-api";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import ChallengesSection from "@/sections/Projects/Details/ChallengesSection";
import GallerySection from "@/sections/Projects/Details/GallerySection";
import ProcessSection from "@/sections/Projects/Details/ProcessSection";
import ProjectCtaSection from "@/sections/Projects/Details/ProjectCtaSection";
import ProjectHeroSection from "@/sections/Projects/Details/ProjectHeroSection";
import ProjectLocalSeoLinks from "@/sections/Projects/Details/ProjectLocalSeoLinks";
import ProjectOverviewSection from "@/sections/Projects/Details/ProjectOverviewSection";
import ProjectVideoSection from "@/sections/Projects/Details/ProjectVideoSection";
import RelatedProjectsSection from "@/sections/Projects/Details/RelatedProjectsSection";
import ResultsSection from "@/sections/Projects/Details/ResultsSection";
import SolutionsSection from "@/sections/Projects/Details/SolutionsSection";

type ProjectPageProps = {
    params: Promise<{ slug: string; locale?: string }>;
};

export async function generateMetadata({
    params,
}: ProjectPageProps): Promise<Metadata> {
    const { slug, locale: routeLocale } = await params;
    const [{ branding, locale, translations }, project] = await Promise.all([
        getSiteSettings(),
        getLocalizedProject(slug, routeLocale),
    ]);
    const siteName = branding.siteName;

    if (!project) {
        await confirmBackendResourceNotFound(
            `/projects/${encodeURIComponent(slug)}`,
            { locale: routeLocale || locale },
        );

        return {
            title: withSiteTitle(
                translateText(
                    translations,
                    "meta.project.notFound",
                    locale,
                    null,
                ),
                siteName,
            ),
            robots: { index: false, follow: false },
        };
    }

    const baseTitle = project.seo?.title || project.title || project.name;
    const title =
        project.city &&
        !baseTitle.toLocaleLowerCase().includes(project.city.toLocaleLowerCase())
            ? `${baseTitle} — ${project.city}`
            : baseTitle;
    const keywords = Array.from(
        new Set(
            [
                ...(project.seo?.keywords ?? []),
                project.city,
                project.objectType,
                ...((project.equipment ?? []).flatMap((item) => [
                    item.name,
                    item.model,
                ])),
            ].filter((item): item is string => Boolean(item && item.trim())),
        ),
    );

    return createMetadata({
        title,
        description:
            project.seo?.description ||
            project.seoDescription ||
            project.description,
        path: `/projects/${project.slug}`,
        locale,
        keywords,
        image:
            project.seo?.image ||
            project.image ||
            branding.defaultImage ||
            undefined,
        siteName,
        type: "article",
        noindex: Boolean(project.seo?.noindex),
    });
}

export default async function ProjectDetailPage({ params }: ProjectPageProps) {
    const { slug, locale: routeLocale } = await params;
    const [{ locale, socialSharing }, project, allLocalLandings] = await Promise.all([
        getSiteSettings(),
        getLocalizedProject(slug, routeLocale),
        getLocalServiceLandings(),
    ]);

    if (!project) {
        await confirmBackendResourceNotFound(
            `/projects/${encodeURIComponent(slug)}`,
            { locale: routeLocale || locale },
        );
        notFound();
    }

    const projectLocalLandings = allLocalLandings.filter((landing) =>
        landing.projects.some((linkedProject) => linkedProject.slug === project.slug),
    );

    const relatedProjects = (
        await Promise.all(
            project.related.map(async (relatedProject) => {
                if (
                    !relatedProject.slug ||
                    relatedProject.slug === project.slug
                ) {
                    return null;
                }

                const resolvedProject = await getLocalizedProject(
                    relatedProject.slug,
                    routeLocale,
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
                        resolvedProject.name,
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
            <ProjectDetailSchema
                localLandings={projectLocalLandings}
                project={project}
            />
            <ProjectHeroSection project={project} />
            <ProjectVideoSection project={project} />
            <ProjectOverviewSection project={project} />
            <ProjectLocalSeoLinks
                landings={projectLocalLandings}
                locale={locale}
            />
            {socialSharing.enabled &&
            socialSharing.showOnProjects &&
            socialSharing.buttons.length ? (
                <ContentShareButtons
                    buttons={socialSharing.buttons}
                    heading={socialSharing.title}
                    locale={locale}
                    pageTitle={project.title || project.name}
                />
            ) : null}
            <ChallengesSection project={project} />
            <SolutionsSection project={project} />
            <ProcessSection project={project} />
            <GallerySection project={project} />
            <ResultsSection project={project} />
            {relatedProjects.length ? (
                <RelatedProjectsSection projects={relatedProjects} />
            ) : null}
            <ProjectCtaSection />
        </article>
    );
}
