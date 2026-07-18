type ProjectsSectionHeaderProps = {
    title: string;
    description?: string;
    centered?: boolean;
};

export default function ProjectsSectionHeader({
    title,
    description,
    centered = false,
}: ProjectsSectionHeaderProps) {
    if (!title && !description) return null;

    return (
        <header
            className={`${centered ? "mx-auto text-center" : ""} mb-unit-lg max-w-3xl`}
        >
            {title ? (
                <h2 className="mb-2 font-headline-lg text-headline-lg text-white">
                    {title}
                </h2>
            ) : null}
            {description ? (
                <p className="font-body-md text-body-md text-on-surface-variant">
                    {description}
                </p>
            ) : null}
        </header>
    );
}
