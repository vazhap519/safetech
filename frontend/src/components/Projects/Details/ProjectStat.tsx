export default function ProjectStat({
    label,
    value,
}: {
    label: string;
    value: string;
}) {
    if (!value && !label) return null;

    return (
        <div className="glass-card rounded-xl p-unit-md text-center">
            {value ? (
                <strong className="block font-display-lg-mobile text-display-lg-mobile text-secondary-fixed-dim">
                    {value}
                </strong>
            ) : null}
            {label ? (
                <span className="font-label-md text-label-md text-on-surface-variant">
                    {label}
                </span>
            ) : null}
        </div>
    );
}
