import ProcessStep from "@/components/Projects/Details/ProcessStep";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function ProcessSection({ project }: { project: ProjectDetail }) {
    return (
        <section className="overflow-hidden py-unit-xl" aria-labelledby="implementation-title">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <h2 className="mb-12 font-headline-xl text-headline-xl" id="implementation-title">იმპლემენტაციის პროცესი</h2>
                <ol className="scrollbar-hide flex snap-x gap-unit-md overflow-x-auto pb-8">
                    {project.process.map((step, index) => <ProcessStep description={step.description} index={index} key={step.title} last={index === project.process.length - 1} title={step.title} />)}
                </ol>
            </div>
        </section>
    );
}
