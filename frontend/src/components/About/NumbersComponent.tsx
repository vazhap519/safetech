export default function NumbersComponent({
    label,
    progressClassName,
    value,
}: {
    label: string;
    progressClassName: string;
    value: string;
}) {
    return (
        <div className="glass-card group relative overflow-hidden rounded-2xl p-unit-md text-center sm:p-unit-lg">
            <div className="absolute inset-0 bg-primary/5 opacity-0 transition-opacity group-hover:opacity-100" />
            {value ? (
                <div className="mb-2 text-headline-xl font-display-lg text-primary">
                    {value}
                </div>
            ) : null}
            {label ? (
                <div className="text-label-md uppercase tracking-widest text-on-surface-variant">
                    {label}
                </div>
            ) : null}
            <div className="mt-4 flex justify-center">
                <div className="h-1 w-12 overflow-hidden rounded-full bg-primary/20">
                    <div className={`h-full bg-primary ${progressClassName}`} />
                </div>
            </div>
        </div>
    );
}
