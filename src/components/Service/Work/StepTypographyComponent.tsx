import Typography from "@/components/ui/Typography";

export default function StepTypographyComponent() {
    return (
        <>
            <div
                className="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-headline-md mb-unit-md relative z-10 border-4 border-background">1
            </div>
            <Typography as="h3" variant="step-title">
                კონსულტაცია
            </Typography>

            <Typography as="p" variant="step-description">
                საჭიროებების განსაზღვრა
            </Typography>
            </>


)
}