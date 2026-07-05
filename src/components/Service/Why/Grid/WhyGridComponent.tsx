import Typography from "@/components/ui/Typography";

export default function WhyGridComponent() {
    return (
        <div className="glass-card p-unit-lg rounded-2xl text-center">
            <span className="material-symbols-outlined text-primary text-[48px] mb-unit-md">shield</span>
            <Typography as={"h5"} variant={"section-title"} className={"font-headline-md text-[20px] mb-2"}>
                უმაღლესი დაცვა
            </Typography>
            <Typography as={"p"} variant={"description"} className={"font-body-md text-body-md text-on-surface-variant"}>
                მონაცემთა და ფიზიკური უსაფრთხოების
                გარანტია.
            </Typography>
        </div>
    )
}