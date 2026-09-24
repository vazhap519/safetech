import ProjectDetailCard from "@/components/Projects/Details/ProjectDetailCard";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ChallengesSection({
    project,
}: {
    project: ProjectDetail;
}) {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "project.detail.challenges.title",
        locale,
        null,
    );
    const cards = project.challenges.filter(
        (card) => card.title || card.description,
    );

    if (!cards.length) return null;

    return (
        <section
            aria-labelledby={title ? "challenges-title" : undefined}
            className="mx-auto max-w-container-max px-4 py-10 sm:px-6 sm:py-14 lg:px-margin-desktop"
        >
            {title ? (
                <h2
                    className="mb-7 text-center font-headline-xl text-headline-xl sm:mb-9"
                    id="challenges-title"
                >
                    {title}
                </h2>
            ) : null}
            <div className={`grid gap-5 sm:gap-gutter ${cards.length === 1 ? "mx-auto max-w-4xl" : cards.length === 2 ? "md:grid-cols-2" : "md:grid-cols-3"}`}>
                {cards.map((card) => (
                    <ProjectDetailCard
                        card={card}
                        key={`${card.title}-${card.description}`}
                        tone="danger"
                    />
                ))}
            </div>
        </section>
    );
}
