import Typography from "@/components/ui/Typography";

export default function Benefit(){
    return (
        <div className="glass-card p-unit-lg rounded-2xl flex flex-col gap-unit-md">
            <div
                className="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                <span className="material-symbols-outlined" data-icon="shield">shield</span>
            </div>
            <Typography as={"h3"} variant={'section-title'} className="font-headline-md text-headline-md text-white">უსაფრთხოება</Typography>
         <Typography as={"p"} variant={"section-description"} className="font-body-md text-body-md text-on-surface-variant">მაქსიმალურად დაცული მონაცემთა
             ბაზები და დაშიფრული ვიდეო ნაკადები.</Typography>
        </div>

    )
}