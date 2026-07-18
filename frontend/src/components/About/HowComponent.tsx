import Typography from "@/components/ui/Typography";

export default function HowComponent({
    align = "right",
    description,
    title,
}: {
    align?: "left" | "right";
    description: string;
    title: string;
}) {
    return (
        <div className="group flex flex-col items-center gap-unit-md md:flex-row">
            <div
                className={`md:w-1/2 ${
                    align === "right" ? "md:text-right" : "md:order-3"
                }`}
            >
                {title ? (
                    <Typography as="h3" className="text-lg font-bold text-primary">
                        {title}
                    </Typography>
                ) : null}
                {description ? (
                    <Typography as="p" variant="section-description">
                        {description}
                    </Typography>
                ) : null}
            </div>
            <div className="z-10 h-4 w-4 rounded-full bg-primary shadow-[0_0_15px_rgba(37,99,235,0.8)]" />
            <div className="md:w-1/2" />
        </div>
    );
}
