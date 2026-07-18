import ResultCard from "@/components/Projects/Details/ResultCard";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ResultsSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "project.detail.results.title",
        locale,
        null,
    );
    const results = project.results.filter(
        (result) => result.value || result.title || result.description,
    );

    if (!results.length) return null;

    return (
        <section
            aria-labelledby={title ? "results-title" : undefined}
            className="bg-surface-container-lowest/50 px-margin-desktop py-unit-xl"
        >
            <div className="mx-auto max-w-container-max">
                {title ? (
                    <h2
                        className="mb-12 text-center font-headline-xl text-headline-xl"
                        id="results-title"
                    >
                        {title}
                    </h2>
                ) : null}
                <div className="grid gap-unit-xl md:grid-cols-3">
                    {results.map((result) => (
                        <ResultCard
                            key={`${result.value}-${result.title}`}
                            result={result}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
}
