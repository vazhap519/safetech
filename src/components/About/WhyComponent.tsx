import Typography from "@/components/ui/Typography";

export default function WhyComponent({
    description,
    icon,
    title,
}: {
    description: string;
    icon: string;
    title: string;
}) {
    return (
        <div className="glass-card rounded-2xl p-unit-lg text-center">
            <span className="material-symbols-outlined text-3xl text-primary">
                {icon}
            </span>
            <div>
                <Typography as="h3" variant="why-component-header">
                    {title}
                </Typography>
                <Typography as="p" variant="description">
                    {description}
                </Typography>
            </div>
        </div>
    );
}
