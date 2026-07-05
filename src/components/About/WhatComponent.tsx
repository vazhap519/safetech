import Typography from "@/components/ui/Typography";

export default function WhatComponent() {
    return (
        <div className="p-unit-lg border-l border-primary/30 hover:border-primary transition-colors">
            <div className="text-primary font-mono-sm mb-unit-sm">01 / PHILOSOPHY</div>
            <Typography
                as="h3"
                className="
        text-xl
        md:text-2xl
        font-headline-md
        mb-4
    "
            >  ინფრასტრუქტურა პირველ
                ადგილზე</Typography>
            <Typography as="p" variant={"section-description"}>
                ჩვენ გვჯერა, რომ პროგრამული უზრუნველყოფა მხოლოდ მაშინ არის
                ეფექტური, როცა აპარატურული ნაწილი უნაკლოა.
            </Typography>
        </div>
    )
}