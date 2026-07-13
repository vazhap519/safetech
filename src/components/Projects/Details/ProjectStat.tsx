export default function ProjectStat({ value, label }: { value: string; label: string }) {
    return (
        <div className="glass-card rounded-xl p-unit-md text-center">
            <strong className="block font-display-lg-mobile text-display-lg-mobile text-secondary-fixed-dim">{value}</strong>
            <span className="font-label-md text-label-md text-on-surface-variant">{label}</span>
        </div>
    );
}
