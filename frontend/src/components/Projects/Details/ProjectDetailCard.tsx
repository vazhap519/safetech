import Icon from "@/components/ui/Icon";
import type { ProjectDetailCard as ProjectDetailCardType } from "@/lib/projectDetails";

type ProjectDetailCardProps = {
    card: ProjectDetailCardType;
    tone?: "danger" | "solution";
};

export default function ProjectDetailCard({
    card,
    tone = "solution",
}: ProjectDetailCardProps) {
    const danger = tone === "danger";

    if (!card.title && !card.description) return null;

    return (
        <article
            className={`${card.featured ? "md:col-span-2 lg:row-span-2 lg:col-span-2" : ""} glass-card relative flex h-full min-h-52 flex-col justify-between overflow-hidden rounded-xl p-unit-lg ${danger ? "border-l-4 border-l-error/40" : "hover:border-primary-container"}`}
        >
            {card.featured ? (
                <div
                    aria-hidden="true"
                    className="ambient-glow -bottom-20 -right-20 h-[300px] w-[300px] bg-secondary-fixed-dim"
                />
            ) : null}
            <Icon
                className={`relative text-4xl ${danger ? "text-error" : "text-secondary-fixed-dim"}`}
                name={card.icon}
            />
            <div className="relative mt-unit-lg">
                {card.title ? (
                    <h3
                        className={`${card.featured ? "text-headline-xl" : "text-headline-md"} mb-3 font-headline-md text-white`}
                    >
                        {card.title}
                    </h3>
                ) : null}
                {card.description ? (
                    <p className="font-body-md text-body-md leading-relaxed text-on-surface-variant">
                        {card.description}
                    </p>
                ) : null}
            </div>
        </article>
    );
}
