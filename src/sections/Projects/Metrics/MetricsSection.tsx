import ProjectMetric from "@/components/Projects/ProjectMetric";
import { projectMetrics } from "@/lib/projects";

export default function MetricsSection() {
    return (
        <section aria-label="შესრულებული სამუშაოების მაჩვენებლები" className="border-y border-outline-variant/10 bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="grid grid-cols-2 gap-gutter md:grid-cols-4">
                    {projectMetrics.map((metric) => <ProjectMetric key={metric.label} metric={metric} />)}
                </div>
            </div>
        </section>
    );
}
