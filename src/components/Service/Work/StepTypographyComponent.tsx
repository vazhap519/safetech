import Typography from "@/components/ui/Typography";

type StepTypographyComponentProps = {
    description: string;
    index: number;
    title: string;
};

export default function StepTypographyComponent({
    description,
    index,
    title,
}: StepTypographyComponentProps) {
    return (
        <>
            <div
                className="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-headline-md mb-unit-md relative z-10 border-4 border-background">{index}
            </div>
            <Typography as="h3" variant="step-title">
                {title}
            </Typography>

            <Typography as="p" variant="step-description">
                {description}
            </Typography>
            </>


)
}
