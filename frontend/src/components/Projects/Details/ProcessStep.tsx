export default function ProcessStep({
    description,
    index,
    last,
    fluid = false,
    stepLabel,
    title,
}: {
    description: string;
    index: number;
    last: boolean;
    fluid?: boolean;
    stepLabel: string;
    title: string;
}) {
    if (!title && !description) return null;

    return (
        <li
            className={`glass-card min-w-0 rounded-2xl border-t-4 p-5 sm:p-unit-lg ${fluid ? "w-full" : "w-[86vw] min-w-[86vw] max-w-[360px] shrink-0 snap-start sm:w-[340px] sm:min-w-[340px]"} ${index === 0 || last ? "border-t-primary-container" : "border-t-outline-variant/30"}`}
        >
            {stepLabel ? (
                <p className="mb-2 font-mono-sm text-mono-sm text-primary">
                    {stepLabel} {String(index + 1).padStart(2, "0")}
                </p>
            ) : null}
            {title ? (
                <h3 className="mb-3 break-words font-headline-md text-[22px] font-semibold leading-snug text-white sm:mb-4 sm:text-headline-md">
                    {title}
                </h3>
            ) : null}
            {description ? (
                <p className="break-words text-[16px] leading-7 text-on-surface-variant sm:font-body-md sm:text-body-md sm:leading-relaxed">
                    {description}
                </p>
            ) : null}
        </li>
    );
}
