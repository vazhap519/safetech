import Typography from "@/components/ui/Typography";

export default function HeroTypography() {


    return (
        <>

            <>

                <Typography
                    as="h1"
                    variant="hero"
                    className="
        max-w-4xl
        mx-auto
        mb-unit-md
        text-center
    "
                >
                    ვქმნით უსაფრთხო და{" "}
                    <span className="text-primary glow-cyan">
        საიმედო ინფრასტრუქტურას
    </span>
                </Typography>
                <Typography as="p" variant="section-description"> SafeTech ეხმარება ბიზნესებს უსაფრთხოების, ქსელური და
                    IT ინფრასტრუქტურის თანამედროვე სისტემების
                    შექმნაში.</Typography>
            </>
        </>
    )
}