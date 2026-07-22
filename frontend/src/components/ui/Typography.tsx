import { ElementType, HTMLAttributes, ReactNode } from "react";
import clsx from "clsx";

type TypographyVariant =
    | "hero"
    | "section-title"
    | "cta-title"
    | "contact-title"
    | "contact-intro-title"
    | "contact-support-title"
    | "contact-faq"
    | "contact-final-cta"
    | "contact-final"
    | "footer-title"
    | "description"
    | "section-description"
    | "why-component-header"
    | "[slug]-hero"
    | "step-title"
    | "step-description";

type TypographyProps = HTMLAttributes<HTMLElement> & {
    as?: ElementType;
    children: ReactNode;
    variant?: TypographyVariant;
};

const variantClasses: Record<TypographyVariant, string> = {
    "step-title": "mb-2 font-headline-md text-xl",
    "step-description":
        "font-body-md text-body-md leading-relaxed text-on-surface-variant",
    "footer-title":
        "font-label-md text-[15px] font-semibold text-primary",
    "why-component-header":
        "mb-2 font-headline-md text-xl sm:text-[22px]",
    "[slug]-hero":
        "mb-6 font-display text-[34px] font-semibold leading-[1.16] sm:text-[40px] md:text-[48px] lg:text-[54px]",
    description:
        "max-w-xl font-body text-base leading-[1.7] text-on-surface-variant md:text-[17px]",
    "section-description":
        "mt-4 text-base leading-[1.7] text-on-surface-variant md:text-[17px]",
    hero:
        "font-display-lg text-[34px] font-semibold leading-[1.16] sm:text-[42px] md:text-[48px] lg:text-[52px] 2xl:text-[58px]",
    "section-title":
        "font-headline-xl text-[30px] font-semibold leading-[1.22] sm:text-[34px] md:text-[40px] lg:text-[44px]",
    "cta-title":
        "font-display-lg text-[30px] font-semibold leading-[1.18] sm:text-[36px] md:text-[44px] lg:text-[50px]",
    "contact-title":
        "mb-unit-md font-display-lg text-[34px] font-semibold leading-[1.16] text-primary sm:text-[42px] md:text-[48px] lg:text-[54px]",
    "contact-intro-title":
        "mb-unit-lg font-headline-xl text-[28px] font-semibold leading-tight text-primary sm:text-[32px] md:text-[38px] lg:text-[42px]",
    "contact-support-title":
        "mb-unit-lg font-headline-xl text-[28px] font-semibold leading-tight text-primary sm:text-[32px] md:text-[38px] lg:text-[42px]",
    "contact-faq":
        "mb-unit-xl text-center font-headline-xl text-[30px] font-semibold leading-tight text-primary sm:text-[34px] md:text-[40px] lg:text-[44px]",
    "contact-final-cta":
        "mb-6 font-display-lg text-[30px] font-semibold leading-[1.18] text-on-surface sm:text-[36px] md:text-[44px] lg:text-[50px]",
    "contact-final":
        "rounded-xl bg-primary-container px-6 py-3 text-sm font-bold text-on-primary-container transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(37,99,235,0.4)] sm:px-8 sm:text-base md:px-10 md:py-4 md:text-lg",
};

export default function Typography({
    as: Component = "p",
    children,
    className,
    variant = "section-title",
    ...props
}: TypographyProps) {
    return (
        <Component
            className={clsx(
                "text-on-surface",
                variantClasses[variant],
                className,
            )}
            {...props}
        >
            {children}
        </Component>
    );
}
