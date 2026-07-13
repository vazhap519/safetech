import ResultCard from "@/components/Projects/Details/ResultCard";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function ResultsSection({ project }: { project: ProjectDetail }) {
    return (
        <section className="bg-surface-container-lowest/50 px-margin-desktop py-unit-xl" aria-labelledby="results-title">
            <div className="mx-auto max-w-container-max">
                <h2 className="mb-12 text-center font-headline-xl text-headline-xl" id="results-title">შედეგები</h2>
                <div className="grid gap-unit-xl md:grid-cols-3">{project.results.map((result) => <ResultCard key={result.title} result={result} />)}</div>
            </div>
        </section>
    );
}
