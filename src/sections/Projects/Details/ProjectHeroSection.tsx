import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import Image from "@/components/ui/Image";
import ProjectBreadcrumb from "@/components/Projects/Details/ProjectBreadcrumb";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function ProjectHeroSection({ project }: { project: ProjectDetail }) {
    return (
        <>
            <section className="mx-auto mt-8 max-w-container-max px-margin-desktop py-unit-sm"><ProjectBreadcrumb current={project.name} /></section>
            <section className="relative mx-auto max-w-container-max overflow-hidden px-margin-desktop py-unit-lg">
                <div aria-hidden="true" className="ambient-glow -right-24 -top-48 h-[500px] w-[500px] bg-primary-container" />
                <div className="grid items-center gap-unit-xl lg:grid-cols-2">
                    <div>
                        <p className="mb-6 inline-flex items-center gap-2 rounded-full border border-primary-container/30 bg-primary-container/10 px-3 py-1 font-mono-sm text-mono-sm text-primary-fixed-dim">
                            <span aria-hidden="true" className="h-2 w-2 rounded-full bg-primary-container animate-pulse" /> Enterprise Infrastructure
                        </p>
                        <h1 className="mb-8 font-display-lg text-display-lg-mobile leading-tight text-white md:text-display-lg">{project.title}</h1>
                        <p className="mb-unit-lg max-w-2xl text-body-lg leading-relaxed text-on-surface-variant">{project.description}</p>
                        <dl className="mb-10 grid grid-cols-1 gap-unit-md border-l border-outline-variant/30 pl-6 min-[420px]:grid-cols-3">
                            {project.meta.map((item) => <div key={item.label}><dt className="mb-1 font-mono-sm text-mono-sm uppercase tracking-widest text-outline">{item.label}</dt><dd className="font-headline-md text-headline-md">{item.value}</dd></div>)}
                        </dl>
                        <div className="flex flex-col gap-unit-md sm:flex-row">
                            <ConsultationTrigger className="rounded-xl bg-primary-container px-6 py-4 text-center font-label-md text-on-primary-container transition-shadow hover:shadow-2xl hover:shadow-primary-container/40">მსგავსი გადაწყვეტის მოთხოვნა →</ConsultationTrigger>
                            <ConsultationTrigger className="rounded-xl border border-outline-variant bg-surface-container-low px-6 py-4 text-center font-label-md text-on-surface transition-colors hover:bg-surface-variant/30">ექსპერტთან კონსულტაცია</ConsultationTrigger>
                        </div>
                    </div>
                    <div className="group relative">
                        <div className="absolute inset-0 z-10 bg-gradient-to-t from-background via-transparent to-transparent opacity-60" />
                        <Image alt={project.imageAlt} className="aspect-[4/3] h-auto w-full rounded-2xl border border-outline-variant/20 object-cover shadow-2xl transition-transform duration-700 group-hover:scale-[1.01]" height={900} priority sizes="(max-width: 1024px) 100vw, 50vw" src={project.image} unoptimized={project.image.endsWith(".svg")} width={1200} />
                    </div>
                </div>
            </section>
        </>
    );
}
