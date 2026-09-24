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
            className="bg-surface-container-lowest/50 px-4 py-10 sm:px-6 sm:py-14 lg:px-margin-desktop"
        >
            <div className="mx-auto max-w-container-max">
                {title ? (
                    <h2
                        className="mb-7 text-center font-headline-xl text-headline-xl sm:mb-9"
                        id="results-title"
                    >
                        {title}
                    </h2>
                ) : null}
                <div className={`grid gap-5 sm:gap-unit-md ${results.length === 1 ? "mx-auto max-w-4xl" : results.length === 2 ? "md:grid-cols-2" : "md:grid-cols-3"}`}>
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
