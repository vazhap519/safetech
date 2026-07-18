import FeaturedProjectCard from "@/components/Projects/FeaturedProjectCard";
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
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-20 md:px-10 md:py-28 xl:px-margin-desktop">
            {eyebrow || title || description ? (
                <div className="mb-14 text-center md:mb-20">
                    {eyebrow ? (
                        <span className="mb-4 inline-block font-mono-sm text-mono-sm uppercase tracking-[0.3em] text-primary">
                            {eyebrow}
                        </span>
                    ) : null}

                    {title ? (
                        <h2 className="font-headline-xl text-[36px] leading-tight text-on-surface sm:text-[42px] md:text-[52px] xl:text-headline-xl">
                            {title}
                        </h2>
                    ) : null}

                    {description ? (
                        <p className="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-on-surface-variant md:text-lg">
                            {description}
                        </p>
                    ) : null}
                </div>
            ) : null}

            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 md:gap-gutter xl:grid-cols-3">
                {projects.slice(0, 3).map((project) => (
                    <FeaturedProjectCard key={project.title} project={project} />
                ))}
            </div>

            <div className="absolute bottom-[-200px] left-[-200px] -z-10 h-[500px] w-[500px] rounded-full bg-primary/5 blur-[150px]" />
        </section>
    );
}
