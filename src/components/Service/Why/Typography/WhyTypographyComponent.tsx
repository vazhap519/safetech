import Typography from "@/components/ui/Typography";

export default function WhyTypographyComponent() {
    return (
        <div className="text-center mb-unit-xl">
            <Typography as={"h2"} variant={"section-title"} className={"font-headline-lg text-headline-lg text-on-surface mb-unit-sm"}>
                რატომ SafeTech?
            </Typography>
            <Typography as={"p"} variant={"section-description"} className={"font-body-md text-body-md text-on-surface-variant"}>
                ჩვენ ვქმნით საიმედო გარემოს თქვენი
                ბიზნესის განვითარებისთვის
            </Typography>

        </div>
    )
}