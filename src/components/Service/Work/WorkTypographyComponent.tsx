import Typography from "@/components/ui/Typography";

type WorkTypographyComponentProps = {
    title: string;
};

export default function WorkTypographyComponent({
    title,
}: WorkTypographyComponentProps) {
    return (
        <Typography as={"h2"} variant={"section-title"} className={"font-headline-lg text-headline-lg text-center mb-unit-xl"}>
            {title}
        </Typography>
    )
}
