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
        <div className="flex items-start gap-4">
            <Icon className="mt-1 text-primary" name={icon} />
            <div>
                <Typography className="font-headline-md text-headline-md text-on-surface" as={"h3"} variant={'section-title'}>
                    {title}
                </Typography>
                <Typography className="text-on-surface-variant font-body-md" as={"p"} variant={'description'}>
                    {description}
                </Typography>
            </div>
        </div>
    )
}
