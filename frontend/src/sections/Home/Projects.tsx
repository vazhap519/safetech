import FeaturedProjectCard from "@/components/Projects/FeaturedProjectCard";
import Typography from "@/components/ui/Typography";
import { getBackendFeaturedProjects } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Projects() {
    const [projects, { locale, translations }] = await Promise.all([
        getBackendFeaturedProjects(),
        getSiteSettings(),
    ]);

    if (!projects.length) return null;

    const eyebrow = translateText(
        translations,
        "home.projects.eyebrow",
        locale,
        null,
    );
    const title = translateText(translations, "home.projects.title", locale, null);
    const description = translateText(
        translations,
        "home.projects.description",
        locale,
        null,
    );

    return (
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14">
            {eyebrow || title || description ? (
                <div className="mb-10 text-center md:mb-14">
                    {eyebrow ? (
                        <span className="mb-3 inline-block font-mono-sm text-mono-sm font-semibold uppercase text-primary">
                            {eyebrow}
                        </span>
                    ) : null}

                    {title ? (
                        <Typography as="h2" variant="section-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <p className="mx-auto mt-4 max-w-2xl text-base leading-[1.7] text-on-surface-variant md:text-[17px]">
                            {description}
                        </p>
                    ) : null}
                </div>
            ) : null}

            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                {projects.slice(0, 3).map((project) => (
                    <FeaturedProjectCard key={project.slug} project={project} />
                ))}
            </div>

        </section>
    );
}
