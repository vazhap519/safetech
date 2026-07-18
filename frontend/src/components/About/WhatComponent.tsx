import Typography from "@/components/ui/Typography";

export default function WhatComponent({
    description,
    indexLabel,
    title,
}: {
    description: string;
    indexLabel: string;
    title: string;
}) {
    return (
        <div className="border-l border-primary/30 p-unit-lg transition-colors hover:border-primary">
            {indexLabel ? (
                <div className="mb-unit-sm font-mono-sm text-primary">
                    {indexLabel}
                </div>
            ) : null}
            {title ? (
                <Typography
                    as="h3"
                    className="mb-4 text-xl font-headline-md md:text-2xl"
                >
                    {title}
                </Typography>
            ) : null}
            {description ? (
                <Typography as="p" variant="section-description">
                    {description}
                </Typography>
            ) : null}
        </div>
    );
}
