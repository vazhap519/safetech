import Typography from "@/components/ui/Typography";

export default function WhoComponent({
    description,
    icon,
    title,
}: {
    description: string;
    icon: string;
    title: string;
}) {
    return (
        <div className="glass-card flex flex-col gap-unit-md rounded-xl p-unit-lg">
            <span className="material-symbols-outlined text-4xl text-primary">
                {icon}
            </span>
            <Typography as="h3" className="text-xl font-headline-md">
                {title}
            </Typography>
            <Typography as="p" variant="section-description">
                {description}
            </Typography>
        </div>
    );
}
