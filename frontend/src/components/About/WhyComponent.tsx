import Icon from "@/components/ui/Icon";
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
            <Icon className="text-3xl text-primary" name={icon} />
            <div>
                {title ? (
                    <Typography as="h3" variant="why-component-header">
                        {title}
                    </Typography>
                ) : null}
                {description ? (
                    <Typography as="p" variant="description">
                        {description}
                    </Typography>
                ) : null}
            </div>
        </div>
    );
}
