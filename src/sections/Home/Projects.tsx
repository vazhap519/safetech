import FeaturedProjectCard from "@/components/Projects/FeaturedProjectCard";
import { getBackendFeaturedProjects } from "@/lib/backend";

export default async function Projects() {
    const projects = await getBackendFeaturedProjects();
    return (
        <section
            className="
                relative
                overflow-hidden
                py-20
                md:py-28
                px-5
                md:px-10
                xl:px-margin-desktop
                max-w-container-max
                mx-auto
            "
        >

            {/* Section Header */}
            <div className="mb-14 md:mb-20 text-center">

                <span
                    className="
                        text-primary
                        font-mono-sm
                        text-mono-sm
                        uppercase
                        tracking-[0.3em]
                        mb-4
                        inline-block
                    "
                >
                    Featured Work
                </span>

                <h2
                    className="
                        font-headline-xl
                        text-[36px]
                        sm:text-[42px]
                        md:text-[52px]
                        xl:text-headline-xl
                        text-on-surface
                        leading-tight
                    "
                >
                    განხორციელებული პროექტები
                </h2>

                <p
                    className="
                        mt-5
                        text-on-surface-variant
                        text-base
                        md:text-lg
                        max-w-2xl
                        mx-auto
                        leading-relaxed
                    "
                >
                    თანამედროვე უსაფრთხოების, ქსელური და
                    IT ინფრასტრუქტურის წარმატებით
                    განხორციელებული პროექტები.
                </p>
            </div>

            {/* Projects Grid */}
            <div
                className="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    xl:grid-cols-3
                    gap-6
                    md:gap-gutter
                "
            >

                {/* Project Card */}
                {projects.slice(0, 3).map((project) => <FeaturedProjectCard key={project.title} project={project} />)}

            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-200px]
                    left-[-200px]
                    w-[500px]
                    h-[500px]
                    bg-primary/5
                    rounded-full
                    blur-[150px]
                    -z-10
                "
            ></div>
        </section>
    );
}
