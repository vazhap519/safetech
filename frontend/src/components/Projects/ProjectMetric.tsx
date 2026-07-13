import type { ProjectMetric as ProjectMetricType } from "@/lib/projects";

export default function ProjectMetric({ metric }: { metric: ProjectMetricType }) {
    return (
        <div className="p-unit-md text-center">
            <strong className="mb-1 block font-headline-xl text-headline-xl text-primary">{metric.value}</strong>
            <span className="font-label-md text-label-md text-on-surface-variant">{metric.label}</span>
        </div>
    );
}
