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
        <div className="group relative grid grid-cols-[1rem_minmax(0,1fr)] items-start gap-x-unit-md md:grid-cols-[minmax(0,1fr)_1rem_minmax(0,1fr)] md:items-center">
            <div
                className={`col-start-2 row-start-1 min-w-0 md:col-auto ${
                    align === "right" ? "md:text-right" : "md:col-start-3"
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
            <div className="z-10 col-start-1 row-start-1 mt-1 h-3 w-3 rounded-full bg-primary shadow-[0_0_12px_rgba(37,99,235,0.65)] md:col-start-2 md:mt-0 md:h-4 md:w-4 md:justify-self-center" />
            <div
                aria-hidden="true"
                className={`hidden md:block ${
                    align === "right" ? "md:col-start-3" : "md:col-start-1 md:row-start-1"
                }`}
            />
        </div>
    );
}
