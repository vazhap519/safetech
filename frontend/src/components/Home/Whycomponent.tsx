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
        <div className="glass-card rounded-lg border-l-4 border-l-primary p-6">
            <Icon className="mb-4 text-4xl text-primary" name={icon} />
            <Typography as="h3" variant={"why-component-header"}>
                {title}
            </Typography>
            <Typography
                as="p"
                variant="section-description"
                className="mx-0 mt-2 max-w-2xl"
            >
                {description}
            </Typography>
        </div>
    )
}
