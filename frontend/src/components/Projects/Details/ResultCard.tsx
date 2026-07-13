import type { ProjectDetail } from "@/lib/projectDetails";

type Result = ProjectDetail["results"][number];

export default function ResultCard({ result }: { result: Result }) {
    const secondary = result.accent === "secondary";
    return (
        <article className={`glass-card rounded-2xl border-b-4 p-unit-lg text-center ${secondary ? "border-b-secondary-container" : "border-b-primary-container"}`}>
            <strong className={`mb-4 block text-5xl font-bold ${secondary ? "text-secondary-container" : "text-primary-container"}`}>{result.value}</strong>
            <h3 className="mb-2 font-headline-md text-headline-md">{result.title}</h3>
            <p className="leading-relaxed text-on-surface-variant">{result.description}</p>
        </article>
    );
}
