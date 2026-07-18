import Icon from "@/components/ui/Icon";
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
            <Icon className="text-4xl text-primary" name={icon} />
            {title ? (
                <Typography as="h3" className="text-xl font-headline-md">
                    {title}
                </Typography>
            ) : null}
            {description ? (
                <Typography as="p" variant="section-description">
                    {description}
                </Typography>
            ) : null}
        </div>
    );
}
