import { ButtonHTMLAttributes, ReactNode } from "react";
import clsx from "clsx";

type ButtonProps = {
    children: ReactNode;

    variant?:
        | "primary"
        | "secondary"
        | "outline"
        | "ghost"
        | "glass"
        | "hero-primary"
        | "hero-secondary"
        | "cta"
        | "navbar-mobile"
    | "contact-hero"
    | "contact-final"

    size?: "sm" | "md" | "lg";

    fullWidth?: boolean;

    className?: string;
} & ButtonHTMLAttributes<HTMLButtonElement>;

export default function Button({
                                   children,
                                   variant = "primary",
                                   size = "md",
                                   fullWidth = false,
                                   className,
                                   ...props
                               }: ButtonProps) {
    return (
        <button
            className={clsx(
                // BASE
                `
                relative
                inline-flex
                items-center
                justify-center
                overflow-hidden
                transition-all
                duration-300
                active:scale-95
                font-medium
                max-w-full
                text-center
                whitespace-normal
                `,

                // WIDTH
                fullWidth && "w-full",

                // VARIANTS
                variant==="contact-hero"&&
                `
                bg-primary
                     hover:bg-primary-fixed-dim
                      text-on-primary
                      font-label-md
                      text-label-md
                       px-unit-lg
                        py-4
                         rounded-full
                         transition-all duration-300 shadow-[0_0_20px_rgba(37,99,235,0.4)]
                `,
                variant==="contact-final"&&
                `
             bg-primary hover:bg-primary-fixed-dim text-on-primary font-headline-md text-headline-md px-unit-xl py-6 rounded-full transition-all duration-300 shadow-[0_0_40px_rgba(37,99,235,0.3)] hover:scale-105 active:scale-95
                `,
                variant === "primary" &&
                `
                    bg-primary-container
                    text-on-primary-container
                    hover:brightness-110
                    shadow-lg
                    shadow-blue-500/20
                    `,

                variant === "secondary" &&
                `
                    bg-secondary-container
                    text-on-secondary-container
                    hover:brightness-110
                    `,

                variant === "outline" &&
                `
                    border
                    border-outline
                    bg-transparent
                    text-on-background
                    hover:bg-surface-container
                    `,

                variant === "ghost" &&
                `
                    bg-transparent
                    text-on-background
                    hover:bg-surface-container
                    `,

                variant === "glass" &&
                `
                    glass-card
                    text-on-surface
                    hover:bg-white/10
                    hover:-translate-y-1
                    `,

                variant === "hero-primary" &&
                `
                    bg-primary-container
                    text-on-primary-container
                    rounded-xl
                    font-headline-md
                    font-bold
                    hover:-translate-y-1
                    hover:shadow-[0_0_30px_rgba(37,99,235,0.4)]
                    `,

                variant === "hero-secondary" &&
                `
                    glass-card
                    text-on-surface
                    rounded-xl
                    font-headline-md
                    font-bold
                    hover:bg-white/10
                    hover:-translate-y-1
                    `,

                variant === "cta" &&
                `
                    bg-primary-container
                    text-on-primary-container
                    rounded-xl
                    font-headline-md
                    font-bold
                    hover:-translate-y-1
                    hover:shadow-[0_0_30px_rgba(37,99,235,0.4)]
                    `,

                variant === "navbar-mobile" &&
                `
                    lg:hidden
                    flex
                    items-center
                    justify-center
                    w-11
                    h-11
                    rounded-xl
                    border
                    border-outline-variant/30
                    bg-surface-container/50
                    text-on-surface
                    backdrop-blur-xl
                    hover:bg-surface-container-high
                    `,

                // SIZES
                size === "sm" &&
                `
                    px-4
                    py-2
                    text-sm
                    rounded-lg
                    `,

                size === "md" &&
                `
                    px-6
                    py-3
                    text-base
                    rounded-xl
                    `,

                size === "lg" &&
                `
                    px-8
                    md:px-10
                    py-4
                    text-base
                    md:text-lg
                    rounded-xl
                    `,

                className
            )}
            {...props}
        >
            {children}
        </button>
    );
}
