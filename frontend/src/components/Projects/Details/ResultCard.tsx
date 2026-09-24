import type { ProjectDetail } from "@/lib/projectDetails";

type Result = ProjectDetail["results"][number];

export default function ResultCard({ result }: { result: Result }) {
    const secondary = result.accent === "secondary";
    const metric = result.value.trim();
    const isCompactMetric = metric.length <= 20 && !/[;\n]/.test(metric);
    // Older AI-generated records can store all project facts inside "value".
    // Present that content as readable copy, not a giant one-word-per-line KPI.
    const factList = !isCompactMetric
        ? metric.split(/[;\n]+/).map((fact) => fact.trim()).filter(Boolean)
        : [];

    if (!result.value && !result.title && !result.description) return null;

    return (
        <article
            className={`glass-card min-w-0 rounded-2xl border-b-4 p-5 sm:p-unit-lg ${secondary ? "border-b-secondary-container" : "border-b-primary-container"}`}
        >
            {isCompactMetric && metric ? (
                <strong
                    className={`mb-4 block break-words text-center text-4xl font-bold sm:text-5xl ${secondary ? "text-secondary-container" : "text-primary-container"}`}
                >
                    {metric}
                </strong>
            ) : null}
            {result.title ? (
                <h3 className="mb-3 break-words text-center font-headline-md text-headline-md">
                    {result.title}
                </h3>
            ) : null}
            {!isCompactMetric && metric ? (
                factList.length > 1 ? (
                    <ul className="mb-4 list-inside list-disc space-y-2 break-words text-left text-base leading-7 text-on-surface-variant [overflow-wrap:anywhere]">
                        {factList.map((fact, index) => (
                            <li key={`${index}-${fact}`}>{fact.replace(/[.;]+$/, "")}</li>
                        ))}
                    </ul>
                ) : (
                    <p className="mb-4 whitespace-pre-line break-words text-left text-base leading-7 text-on-surface-variant [overflow-wrap:anywhere]">
                        {metric}
                    </p>
                )
            ) : null}
            {result.description ? (
                <p className="break-words text-center leading-relaxed text-on-surface-variant">
                    {result.description}
                </p>
            ) : null}
        </article>
    );
}
