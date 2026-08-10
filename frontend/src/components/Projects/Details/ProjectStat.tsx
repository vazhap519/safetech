export default function ProjectStat({
    label,
    value,
}: {
    label: string;
    value: string;
}) {
    if (!value && !label) return null;

    return (
        <div className="glass-card flex min-h-36 min-w-0 flex-col items-center justify-center rounded-2xl p-4 text-center sm:min-h-40 sm:p-unit-md">
            {value ? (
                <strong className="block max-w-full break-words text-[clamp(24px,2.2vw,34px)] font-semibold leading-tight text-secondary-fixed-dim [overflow-wrap:anywhere]">
                    {value}
                </strong>
            ) : null}
            {label ? (
                <span className="mt-3 max-w-full break-words text-[14px] font-medium leading-5 text-on-surface-variant sm:font-label-md sm:text-label-md">
                    {label}
                </span>
            ) : null}
        </div>
    );
}
