import Icon from "@/components/ui/Icon";
import Typography from "@/components/ui/Typography";

export default function WhyComponent({
    icon,
    title,
    description,
}: {
    icon: string;
    title: string;
    description: string;
}) {
    return (
        <div className="p-unit-lg glass-card rounded-2xl border-l-4 border-l-primary">
            <Icon className="mb-4 text-4xl text-primary" name={icon} />
            <Typography as="h3" variant={"why-component-header"}>
                {title}
            </Typography>
            <Typography
                as="p"
                variant="section-description"
                className="max-w-2xl mx-auto"
            >
                {description}
            </Typography>
        </div>
    )
}
