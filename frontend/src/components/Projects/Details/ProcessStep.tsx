export default function ProcessStep({
    description,
    index,
    last,
    stepLabel,
    title,
}: {
    description: string;
    index: number;
    last: boolean;
    stepLabel: string;
    title: string;
}) {
    if (!title && !description) return null;

    return (
        <li
            className={`glass-card min-w-[min(280px,80vw)] snap-start rounded-xl border-t-4 p-unit-lg ${index === 0 || last ? "border-t-primary-container" : "border-t-outline-variant/30"}`}
        >
            {stepLabel ? (
                <p className="mb-2 font-mono-sm text-mono-sm text-primary">
                    {stepLabel} {String(index + 1).padStart(2, "0")}
                </p>
            ) : null}
            {title ? (
                <h3 className="mb-4 font-headline-md text-headline-md text-white">
                    {title}
                </h3>
            ) : null}
            {description ? (
                <p className="font-body-md text-body-md leading-relaxed text-on-surface-variant">
                    {description}
                </p>
            ) : null}
        </li>
    );
}
