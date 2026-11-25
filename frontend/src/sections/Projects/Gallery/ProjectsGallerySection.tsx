import ProjectsSectionHeader from "@/components/Projects/ProjectsSectionHeader";
import ContentFilterGrid from "@/components/filters/ContentFilterGrid";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getBackendFilterCategories } from "@/lib/backend";
import { getLocalizedProjectPage } from "@/lib/project-api";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import { paginatedPath } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";

export default async function ProjectsGallerySection({
    category,
    page = 1,
}: {
    category?: string;
    page?: number;
}) {
    const [projectPage, categories, { locale, translations }] = await Promise.all([
        getLocalizedProjectPage(category, page),
        getBackendFilterCategories("projects"),
        getSiteSettings(),
    ]);

    if (!projectPage.projects.length) return null;

    const pageHref = (targetPage: number) => {
        const base = category
            ? `/projects/category/${encodeURIComponent(category)}`
            : "/projects";
        return targetPage === 1 ? `${base}#content-filter` : `${base}?page=${targetPage}#content-filter`;
    };
    const paginationBase = category
        ? `/projects/category/${encodeURIComponent(category)}`
        : "/projects";
    const previousPath = projectPage.currentPage > 1
        ? paginatedPath(paginationBase, projectPage.currentPage - 1)
        : null;
    const nextPath = projectPage.currentPage < projectPage.lastPage
        ? paginatedPath(paginationBase, projectPage.currentPage + 1)
        : null;

    return (
        <>
            {previousPath ? <link rel="prev" href={localizeHref(previousPath, locale)} /> : null}
            {nextPath ? <link rel="next" href={localizeHref(nextPath, locale)} /> : null}
            <section
                className="bg-surface-container-low/30 py-unit-xl"
                aria-labelledby="projects-gallery-title"
            >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div id="projects-gallery-title">
                    <ProjectsSectionHeader
                        centered
                        title={translateText(
                            translations,
                            "projects.gallery.title",
                            locale,
                            null,
                        )}
                        description={translateText(
                            translations,
                            "projects.gallery.description",
                            locale,
                            null,
                        )}
                    />
                </div>
                <ContentFilterGrid
                    activeCategory={category}
                    categories={categories}
                    items={projectPage.projects}
                    kind="projects"
                />
                {projectPage.lastPage > 1 ? (
                    <nav aria-label="Projects pagination" className="mt-unit-xl flex items-center justify-center gap-unit-sm">
                        {projectPage.currentPage > 1 ? (
                            <LocalizedLink className="glass-card rounded-full px-5 py-2.5 font-semibold" href={pageHref(projectPage.currentPage - 1)} prefetch={false}>
                                {locale === "ka" ? "წინა" : locale === "ru" ? "Назад" : "Previous"}
                            </LocalizedLink>
                        ) : null}
                        <span className="px-3 text-sm text-on-surface-variant">
                            {projectPage.currentPage} / {projectPage.lastPage}
                        </span>
                        {projectPage.currentPage < projectPage.lastPage ? (
                            <LocalizedLink className="rounded-full bg-primary px-5 py-2.5 font-semibold text-on-primary" href={pageHref(projectPage.currentPage + 1)} prefetch={false}>
                                {locale === "ka" ? "მეტის ნახვა" : locale === "ru" ? "Показать ещё" : "View more"}
                            </LocalizedLink>
                        ) : null}
                    </nav>
                ) : null}
            </div>
            </section>
        </>
    );
}
