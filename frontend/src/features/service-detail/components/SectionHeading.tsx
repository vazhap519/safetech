type SectionHeadingProps = {
    children: React.ReactNode;
    centered?: boolean;
    eyebrow?: string;
};

export default function SectionHeading({ children, centered = false, eyebrow }: SectionHeadingProps) {
    if (!eyebrow && !children) return null;

    return (
        <header className={centered ? "mx-auto mb-unit-xl max-w-3xl text-center" : "mb-unit-xl"}>
            {eyebrow && (
                <p className="mb-unit-sm font-label-md text-label-md uppercase tracking-[0.18em] text-primary">
                    {eyebrow}
                </p>
            )}
            {children ? (
                <h2 className="font-headline-xl text-[30px] leading-tight text-white sm:text-[38px] md:text-[52px]">
                    {children}
                </h2>
            ) : null}
        </header>
    );
}
