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
            <span className="material-symbols-outlined text-primary mt-1">
                {icon}
            </span>
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
