import Icon from "@/components/ui/Icon";
import type { ProjectStandard } from "@/lib/projects";

export default function StandardCard({ standard }: { standard: ProjectStandard }) {
    const secondary = standard.accent === "secondary";

    return (
        <article className="glass-card flex flex-col items-center rounded-xl p-unit-lg text-center">
            <Icon
                className={`mb-6 flex h-16 w-16 items-center justify-center rounded-full border p-4 text-3xl ${secondary ? "border-secondary/20 bg-secondary/10 text-secondary" : "border-primary/20 bg-primary/10 text-primary"}`}
                name={standard.icon}
            />
            <h3 className="mb-4 font-headline-md text-headline-md text-white">{standard.title}</h3>
            <p className="text-body-md leading-relaxed text-on-surface-variant">{standard.description}</p>
        </article>
    );
}
