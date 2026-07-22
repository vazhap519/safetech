import Icon from "@/components/ui/Icon";
import Typography from "@/components/ui/Typography";

export default function Infrastructurecomponent({
    icon,
    title,
    description,
}: {
    icon: string;
    title: string;
    description: string;
}) {
    return (
        <div className="flex items-start gap-4 text-left">
            <Icon className="mt-1 text-primary" name={icon} />
            <div>
                <Typography as="h3" variant="why-component-header">
                    {title}
                </Typography>
                <Typography className="mt-1" as="p" variant="description">
                    {description}
                </Typography>
            </div>
        </div>
    )
}
