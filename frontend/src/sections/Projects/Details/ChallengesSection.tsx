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
            className="mx-auto max-w-container-max px-margin-desktop py-unit-xl"
        >
            {title ? (
                <h2
                    className="mb-12 text-center font-headline-xl text-headline-xl"
                    id="challenges-title"
                >
                    {title}
                </h2>
            ) : null}
            <div className="grid gap-gutter md:grid-cols-3">
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
