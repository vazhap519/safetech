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
        <div className="glass-card group relative min-w-0 overflow-hidden rounded-2xl p-5 text-center sm:p-unit-lg">
            <div className="absolute inset-0 bg-primary/5 opacity-0 transition-opacity group-hover:opacity-100" />
            {value ? (
                <div className="relative mb-2 break-words text-[28px] font-display-lg leading-tight text-primary [overflow-wrap:anywhere] sm:text-headline-xl">
                    {value}
                </div>
            ) : null}
            {label ? (
                <div className="relative break-words text-sm font-label-md uppercase leading-relaxed tracking-wide text-on-surface-variant [overflow-wrap:anywhere] sm:text-label-md sm:tracking-widest">
                    {label}
                </div>
            ) : null}
            <div className="relative mt-4 flex justify-center">
                <div className="h-1 w-12 overflow-hidden rounded-full bg-primary/20">
                    <div className={`h-full bg-primary ${progressClassName}`} />
                </div>
            </div>
        </div>
    );
}
